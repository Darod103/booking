<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\CreateBookingRequest;
use App\Entity\Booking;
use App\Entity\Room;
use App\Exception\BookingAlreadyCancelledException;
use App\Exception\BookingAlreadyStartedException;
use App\Exception\BookingConflictException;
use App\Exception\BookingNotFoundException;
use App\Exception\CapacityExceededException;
use App\Exception\InvalidBookingPeriodException;
use App\Exception\InvalidTimeRangeException;
use App\Exception\RoomNotActiveException;
use App\Exception\RoomNotFoundException;
use App\Repository\BookingRepository;
use App\ValueObject\BookingParticipants;
use App\ValueObject\BookingPeriod;
use App\ValueObject\TimeRange;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\Uid\Uuid;

class BookingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookingRepository $bookingRepository,
        private readonly ClockInterface $clock,
    ) {
    }

    /**
     * @throws RoomNotFoundException
     * @throws InvalidTimeRangeException
     * @throws InvalidBookingPeriodException
     * @throws RoomNotActiveException
     * @throws CapacityExceededException
     * @throws BookingConflictException
     */
    public function createBooking(CreateBookingRequest $request): Booking
    {
        $period = BookingPeriod::fromRange(new TimeRange($request->startAt, $request->endAt));
        $participants = new BookingParticipants($request->participants);

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $room = $this->entityManager->find(Room::class, Uuid::fromString($request->roomId), LockMode::PESSIMISTIC_WRITE)
                ?? throw RoomNotFoundException::withId($request->roomId);

            $booking = Booking::create($request->organizerId, $period, $participants, $room);

            if ($this->bookingRepository->existsBlockingOverlap($room, $period->range)) {
                throw BookingConflictException::create();
            }

            $this->entityManager->persist($booking);
            $this->entityManager->flush();
            $connection->commit();
        } catch (\Throwable $e) {
            $connection->rollBack();
            throw $e;
        }

        return $booking;
    }

    /**
     * @throws BookingNotFoundException
     * @throws BookingAlreadyCancelledException
     * @throws BookingAlreadyStartedException
     */
    public function cancelBooking(string $bookingId): Booking
    {
        $booking = $this->bookingRepository->find(Uuid::fromString($bookingId))
            ?? throw BookingNotFoundException::withId($bookingId);

        $booking->cancel($this->clock->now());
        $this->entityManager->flush();

        return $booking;
    }
}
