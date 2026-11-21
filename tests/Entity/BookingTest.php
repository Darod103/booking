<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Booking;
use App\Entity\Room;
use App\Enum\BookingStatus;
use App\Exception\BookingAlreadyCancelledException;
use App\Exception\BookingAlreadyStartedException;
use App\Exception\CapacityExceededException;
use App\Exception\RoomNotActiveException;
use App\ValueObject\BookingParticipants;
use App\ValueObject\BookingPeriod;
use App\ValueObject\TimeRange;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    private Room $room;
    private string $organizerId = 'organizer-1';
    private BookingPeriod $period;

    protected function setUp(): void
    {
        $this->room = new Room()->setCapacityInt(10);
        $range = new  TimeRange(
            new \DateTimeImmutable('2026-01-01 10:00:00'),
            new \DateTimeImmutable('2026-01-01 11:00:00'),
        );
        $this->period = BookingPeriod::fromRange($range);
    }

    #[TestDox('Активную комнату можно забронировать')]
    public function testActiveRoomCanBeBooked(): void
    {
        $booking = Booking::create($this->organizerId,$this->period,new BookingParticipants(10),$this->room);
        $this->assertSame(BookingStatus::Active,$booking->getStatus());
    }

    #[TestDox('Нельзя забронировать не активную комнату')]
    public function testCannotBookRoomIfItIsAlreadyBooked(): void
    {
        $room = $this->room->setIsActive(false);
        $this->expectException(RoomNotActiveException::class);
        Booking::create($this->organizerId,$this->period,new BookingParticipants(10),$room);

    }

    #[TestDox('Нельзя забронировать комнату, если количество участников превышает вместимость')]
    public function testCannotBookRoomIfParticipantsCountExceedsCapacity(): void
    {
        $room = $this->room->setCapacityInt(10);
        $this->expectException(CapacityExceededException::class);
        Booking::create($this->organizerId,$this->period,new BookingParticipants(11),$room);
    }
    #[TestDox('Активную будущую бронь можно отменить')]
    public function testCanCancelActiveFutureBooking(): void
    {
        $booking = Booking::create(
            $this->organizerId,
            $this->period,
            new BookingParticipants(10),
            $this->room,
        );

        $booking->cancel(new \DateTimeImmutable('2026-01-01 09:00:00'));

        $this->assertSame(BookingStatus::Cancelled, $booking->getStatus());
    }

    #[TestDox('Повторная отмена брони отклоняется')]
    public function testCannotCancelBookingTwice(): void
    {
        $booking = Booking::create($this->organizerId, $this->period, new BookingParticipants(10), $this->room);
        $now = new \DateTimeImmutable('2026-01-01 09:00:00');
        $booking->cancel($now);

        $this->expectException(BookingAlreadyCancelledException::class);
        $booking->cancel($now);
    }

    #[TestDox('Нельзя отменить бронь с момента её начала')]
    #[TestWith(['2026-01-01 10:00:00'])]
    #[TestWith(['2026-01-01 10:30:00'])]
    public function testCannotCancelStartedBooking(string $now): void
    {
        $booking = Booking::create($this->organizerId, $this->period, new BookingParticipants(10), $this->room);

        $this->expectException(BookingAlreadyStartedException::class);
        $booking->cancel(new \DateTimeImmutable($now));
    }

    #[TestDox('Статус закрыт от прямого изменения и не имеет публичного сеттера')]
    public function testStatusCannotBeChangedDirectlyFromOutside(): void
    {
        $class = new \ReflectionClass(Booking::class);

        $this->assertTrue($class->getProperty('status')->isPrivate());
        $this->assertFalse($class->hasMethod('setStatus') && $class->getMethod('setStatus')->isPublic());
    }
}
