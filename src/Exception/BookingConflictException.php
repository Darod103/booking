<?php
declare(strict_types=1);

namespace App\Exception;

class BookingConflictException extends \Exception
{
    public static function create() :self
    {
        return new self('Комната уже занята на это время');
    }
}
