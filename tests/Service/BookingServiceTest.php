<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\CreateBookingRequest;
use App\Entity\Room;
use App\Exception\BookingConflictException;
use App\Repository\BookingRepository;
use App\Service\BookingService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Uid\Uuid;

class BookingServiceTest extends TestCase
{
    private Room $room;

    protected function setUp(): void
    {
        $this->room = (new Room())->setCapacityInt(10);
    }

    #[TestDox('Нельзя забронировать комнату, если время пересекается с активной бронью')]
    public function testCannotBookRoomIfItIsAlreadyBookedOnSpecifiedTime(): void
    {
        $roomId = Uuid::v7();
        $request = new CreateBookingRequest(
            roomId: $roomId->toRfc4122(),
            startAt: new \DateTimeImmutable('2030-01-01 10:00:00 UTC'),
            endAt: new \DateTimeImmutable('2030-01-01 11:00:00 UTC'),
            organizerId: 'organizer-1',
            participants: 5,
        );

        $connection = $this->createStub(Connection::class);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('getConnection')->willReturn($connection);
        $entityManager->method('find')->willReturn($this->room);
        $repository = $this->createStub(BookingRepository::class);
        $repository->method('existsBlockingOverlap')->willReturn(true);

        $service = new BookingService(
            $entityManager,
            $repository,
            new MockClock('2030-01-01 09:00:00 UTC'),
        );

        $this->expectException(BookingConflictException::class);
        $service->createBooking($request);
    }
}
