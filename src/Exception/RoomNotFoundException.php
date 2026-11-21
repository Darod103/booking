<?php
declare(strict_types=1);

namespace App\Exception;

class RoomNotFoundException extends \Exception
{
    public static function withId(string $roomId): self
    {
        return new self("Комната с таким id $roomId не найдена");
    }
}
