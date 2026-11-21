<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidBookingPeriodException extends \Exception
{
    public static function tooLong(string $maxDuration): self
    {
        return new self(sprintf(
            'Бронирование не может длиться дольше %s',
            new \DateInterval($maxDuration)->format('%h ч.'),
        ));
    }
}
