<?php

namespace App\Entity;

use App\Enum\BookingStatus;
use App\Exception\BookingConflictException;
use App\Exception\CapacityExceededException;
use App\Exception\RoomNotActiveException;
use App\Repository\BookingRepository;
use App\ValueObject\BookingDates;
use App\ValueObject\BookingParticipants;
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
    private ?string $organizerId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $participants = 1;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    #[ORM\Column(enumType: BookingStatus::class)]
    private BookingStatus $status = BookingStatus::Active;

    public static function create(
        string $organizerId,
        BookingDates $dates,
        BookingParticipants $participants,
        Room $room,
        array $existingBookings = []
    ): self {
        if (empty($organizerId)) {
            throw new \InvalidArgumentException('Организатор не может быть пустым');
        }

        if (!$room->isActive()) {
            throw RoomNotActiveException::create();
        }

        if (!$participants->fitsIn($room->getCapacity())) {
            throw CapacityExceededException::withLimit($room->getCapacity()->toInt());
        }

        foreach ($existingBookings as $booking) {
            if ($booking->overlaps($dates)) {
                throw BookingConflictException::create();
            }
        }

        $self = new self();
        $self->organizerId = $organizerId;
        $self->startAt = $dates->getStartAt();
        $self->endAt = $dates->getEndAt();
        $self->participants = $participants->getValue();
        $self->room = $room;

        return $self;
    }

    private function overlaps(BookingDates $dates): bool
    {
        return new BookingDates($this->startAt, $this->endAt)->overlaps($dates);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOrganizerId(): ?string
    {
        return $this->organizerId;
    }

    public function setOrganizerId(string $organizerId): static
    {
        if (empty($organizerId)) {
            throw new \InvalidArgumentException('Организатор не может быть пустым');
        }
        $this->organizerId = $organizerId;
        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function setDates(BookingDates $dates): static
    {
        $this->startAt = $dates->getStartAt();
        $this->endAt = $dates->getEndAt();
        return $this;
    }

    public function getParticipants(): int
    {
        return $this->participants;
    }

    public function setParticipants(int $participants): static
    {
        $vo = new BookingParticipants($participants);
        $this->participants = $vo->getValue();
        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getStatus(): ?BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function cancel(): void
    {
        if ($this->status === BookingStatus::Cancelled) {
            throw new \InvalidArgumentException('Бронирование уже отменено');
        }

        if ($this->startAt <= new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('Нельзя отменить бронирование которое уже началось');
        }

        $this->status = BookingStatus::Cancelled;
    }
}
