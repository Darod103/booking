<?php

namespace App\Enum;

enum BookingStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';
}
