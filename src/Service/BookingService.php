<?php

namespace App\Service;

use App\Dto\CreateBookingRequest;
use App\Entity\Booking;
use App\Entity\Room;
use App\Exception\BookingConflictException;
use App\Exception\CapacityExceededException;
use App\Exception\InvalidBookingPeriodException;
use App\Exception\RoomNotFoundException;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class BookingService
{
    public function __construct(
        private readonly RoomRepository         $roomRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly BookingRepository      $bookingRepository
    )
    {
    }

    /**
     * @throws InvalidBookingPeriodException
     * @throws RoomNotFoundException
     * @throws CapacityExceededException
     * @throws BookingConflictException
     */
    public function createBooking(CreateBookingRequest $createBookingRequest): Booking
    {
        $room = $this->roomRepository->find(Uuid::fromString($createBookingRequest->roomId));
        if (null === $room) {
            throw RoomNotFoundException::withId($createBookingRequest->roomId);
        }
        $this->validatePeriod($createBookingRequest);
        $this->validateCapacity($createBookingRequest, $room);
        $this->validateNoOverlap($room, $createBookingRequest);
        $booking = new Booking();
        $booking->setRoom($room);
        $booking->setOrganizerId($createBookingRequest->organizerId);
        $booking->setParticipants($createBookingRequest->participants);
        $booking->setStartAt($createBookingRequest->startAt);
        $booking->setEndAt($createBookingRequest->endAt);
        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }

    /**
     * @throws InvalidBookingPeriodException
     */
    private function validatePeriod(CreateBookingRequest $request): void
    {
        if ($request->startAt >= $request->endAt) {
            throw  InvalidBookingPeriodException::create();
        }
    }

    private function validateCapacity(CreateBookingRequest $request, Room $room): void
    {
        if ($request->participants > $room->getCapacity()) {
            throw CapacityExceededException::withLimit($room->getCapacity());
        }
    }

    private function validateNoOverlap(Room $room, CreateBookingRequest $request): void
    {
        $overlappingBookings = $this->bookingRepository->findOverlapping($room, $request->startAt, $request->endAt);
        if (!empty($overlappingBookings)) {
            throw BookingConflictException::create();
        }
    }
}
