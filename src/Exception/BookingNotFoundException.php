<?php
declare(strict_types=1);

namespace App\Exception;

class BookingNotFoundException extends \Exception
{
    public static function withId(string $bookingId): self
    {
        return new self("Бронирование с id $bookingId не найдено");
    }
}
