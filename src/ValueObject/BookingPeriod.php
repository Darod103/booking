<?php
declare(strict_types=1);

namespace App\ValueObject;

use App\Exception\InvalidBookingPeriodException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class BookingPeriod
{
    public const string MAX_DURATION = 'PT8H';

    #[ORM\Embedded(class: TimeRange::class, columnPrefix: false)]
    public TimeRange $range;

    private function __construct(TimeRange $range)
    {
        $this->range = $range;
    }

    /**
     * @throws InvalidBookingPeriodException
     */
    public static function fromRange(TimeRange $range): self
    {
        if ($range->isLongerThan(new \DateInterval(self::MAX_DURATION))) {
            throw InvalidBookingPeriodException::tooLong(self::MAX_DURATION);
        }

        return new self($range);
    }
}
