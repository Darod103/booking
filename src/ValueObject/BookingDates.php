<?php
declare(strict_types=1);

namespace App\ValueObject;

final class BookingDates
{
    private const int MIN_DURATION_HOURS = 1;
    private const int MAX_DURATION_HOURS = 8;

    public function __construct(
        private readonly \DateTimeImmutable $startAt,
        private readonly \DateTimeImmutable $endAt
    ) {
        if ($startAt >= $endAt) {
            throw new \InvalidArgumentException(
                sprintf('Дата начала должна быть раньше даты окончания. Начало: %s, Окончание: %s',
                    $startAt->format('Y-m-d H:i:s'),
                    $endAt->format('Y-m-d H:i:s')
                )
            );
        }

        $duration = $endAt->diff($startAt);
        $hours = $duration->h + ($duration->days * 24);

        if ($hours < self::MIN_DURATION_HOURS) {
            throw new \InvalidArgumentException(
                sprintf('Минимальная длительность бронирования %d часов', self::MIN_DURATION_HOURS)
            );
        }

        if ($hours > self::MAX_DURATION_HOURS) {
            throw new \InvalidArgumentException(
                sprintf('Максимальная длительность бронирования %d часов', self::MAX_DURATION_HOURS)
            );
        }
    }

    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function overlaps(BookingDates $other): bool
    {
        return $this->startAt < $other->endAt && $this->endAt > $other->startAt;
    }

    public function getDuration(): \DateInterval
    {
        return $this->endAt->diff($this->startAt);
    }
}