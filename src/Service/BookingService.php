<?php

namespace App\Service;

use App\Dto\CreateBookingRequest;
use App\Entity\Booking;
use App\Exception\RoomNotFoundException;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use App\ValueObject\BookingDates;
use App\ValueObject\BookingParticipants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class BookingService
{
    public function __construct(
        private readonly RoomRepository $roomRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly BookingRepository $bookingRepository
    ) {
    }

    public function createBooking(CreateBookingRequest $request): Booking
    {
        $room = $this->roomRepository->find(Uuid::fromString($request->roomId))
            ?? throw RoomNotFoundException::withId($request->roomId);

        $dates = new BookingDates($request->startAt, $request->endAt);
        $participants = new BookingParticipants($request->participants);

        $existingBookings = $this->bookingRepository->findOverlapping(
            $room,
            $request->startAt,
            $request->endAt
        );

        $booking = Booking::create(
            $request->organizerId,
            $dates,
            $participants,
            $room,
            $existingBookings
        );

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    public function cancelBooking(string $bookingId): mixed
    {
        $booking = $this->bookingRepository->find(Uuid::fromString($bookingId))
            ?? throw new \Exception('Бронирование не найдено');

        $booking->cancel();
        $this->entityManager->flush();

        return $booking;
    }
}
