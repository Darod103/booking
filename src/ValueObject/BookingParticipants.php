<?php
declare(strict_types=1);

namespace App\ValueObject;

final class BookingParticipants
{
    private const int MIN = 1;
    private const int MAX = 10000;

    public function __construct(private readonly int $value)
    {
        if ($value < self::MIN || $value > self::MAX) {
            throw new \InvalidArgumentException(
                sprintf('Количество участников должно быть от %d до %d, получено %d',
                    self::MIN,
                    self::MAX,
                    $value
                )
            );
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function fitsIn(RoomCapacity $capacity): bool
    {
        return $this->value <= $capacity->toInt();
    }

    public function equals(BookingParticipants $other): bool
    {
        return $this->value === $other->value;
    }
}