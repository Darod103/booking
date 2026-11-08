<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\InvalidTimeRangeException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class TimeRange
{
    #[ORM\Column]
    public \DateTimeImmutable $startAt;

    #[ORM\Column]
    public \DateTimeImmutable $endAt;

    /**
     * @throws InvalidTimeRangeException
     */
    public function __construct(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt)
    {
        if ($startAt >= $endAt) {
            throw InvalidTimeRangeException::startNotBeforeEnd($startAt, $endAt);
        }

        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    public function overlaps(self $other): bool
    {
        return $this->startAt < $other->endAt && $this->endAt > $other->startAt;
    }

    public function startsAfter(\DateTimeImmutable $moment): bool
    {
        return $this->startAt > $moment;
    }

    public function isLongerThan(\DateInterval $duration): bool
    {
        return $this->endAt > $this->startAt->add($duration);
    }
}
