<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\BookingStatus;
use App\Exception\BookingAlreadyCancelledException;
use App\Exception\BookingAlreadyStartedException;
use App\Exception\CapacityExceededException;
use App\Exception\RoomNotActiveException;
use App\Repository\BookingRepository;
use App\ValueObject\BookingParticipants;
use App\ValueObject\BookingPeriod;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private string $organizerId;

    #[ORM\Embedded(class: BookingPeriod::class, columnPrefix: false)]
    private BookingPeriod $period;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $participants;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Room $room;

    #[ORM\Column(enumType: BookingStatus::class)]
    private BookingStatus $status = BookingStatus::Active;

    private function __construct(string $organizerId, BookingPeriod $period, int $participants, Room $room)
    {
        $this->organizerId = $organizerId;
        $this->period = $period;
        $this->participants = $participants;
        $this->room = $room;
    }

    /**
     * @throws RoomNotActiveException
     * @throws CapacityExceededException
     */
    public static function create(
        string $organizerId,
        BookingPeriod $period,
        BookingParticipants $participants,
        Room $room,
    ): self {
        if (trim($organizerId) === '') {
            throw new \InvalidArgumentException('Организатор не может быть пустым');
        }

        if (!$room->isActive()) {
            throw RoomNotActiveException::create();
        }

        if (!$participants->fitsIn($room->getCapacity())) {
            throw CapacityExceededException::withLimit($room->getCapacity()->toInt());
        }

        return new self($organizerId, $period, $participants->toInt(), $room);
    }

    /**
     * @throws BookingAlreadyCancelledException
     * @throws BookingAlreadyStartedException
     */
    public function cancel(\DateTimeImmutable $now): void
    {
        if ($this->isCancelled()) {
            throw BookingAlreadyCancelledException::create();
        }

        if (!$this->period->range->startsAfter($now)) {
            throw BookingAlreadyStartedException::create();
        }

        $this->status = BookingStatus::Cancelled;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::Cancelled;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOrganizerId(): string
    {
        return $this->organizerId;
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->period->range->startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->period->range->endAt;
    }

    public function getParticipants(): int
    {
        return $this->participants;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function getStatus(): BookingStatus
    {
        return $this->status;
    }
}
