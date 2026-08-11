<?php

namespace App\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_NOT_FOUND)]
class RoomNotFoundException extends \Exception
{
    public static function withId(string $roomId): self
    {
        return new self("Комната с таким id $roomId не найдена");
    }
}
