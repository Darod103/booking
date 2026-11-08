<?php
declare(strict_types=1);

namespace App\Exception;

class BookingAlreadyStartedException extends \Exception
{
    public static function create(): self
    {
        return new self('Нельзя отменить бронирование, которое уже началось');
    }
}
