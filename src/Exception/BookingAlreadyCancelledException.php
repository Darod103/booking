<?php
declare(strict_types=1);

namespace App\Exception;

class BookingAlreadyCancelledException extends \Exception
{
    public static function create(): self
    {
        return new self('Бронирование уже отменено');
    }
}
