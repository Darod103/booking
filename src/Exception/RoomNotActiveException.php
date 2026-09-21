<?php

namespace App\Exception;

final class RoomNotActiveException extends \Exception
{
    public static function create(): self
    {
        return new self('Комната не активна и не может быть забронирована');
    }
}