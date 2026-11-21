<?php
declare(strict_types=1);

namespace App\Enum;

enum BookingStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';

    /** @return list<self> */
    public static function blockingStatuses(): array
    {
        return [self::Active];
    }
}
