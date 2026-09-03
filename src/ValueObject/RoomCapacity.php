<?php
declare(strict_types=1);
namespace App\ValueObject;

final class RoomCapacity
{
    private const int MIN_CAPACITY = 1;
    private const int MAX_CAPACITY = 1000;

    public function __construct(private readonly int $value)
    {
        if ($value < self::MIN_CAPACITY || $value > self::MAX_CAPACITY) {
            throw new \InvalidArgumentException(
                sprintf('Вместимость комнаты должна быть между %d и %d, получено %d',
                    self::MIN_CAPACITY,
                    self::MAX_CAPACITY,
                    $value
                )
            );
        }
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(RoomCapacity $other): bool
    {
        return $this->value === $other->value;
    }
}
