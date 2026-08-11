<?php

namespace App\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
class InvalidBookingPeriodException extends \Exception
{
    public static function create(): self
    {
        return new self("Некорректный период бронирования");
    }
}
