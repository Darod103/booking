<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_CONFLICT)]
class BookingConflictException extends \Exception
{
    public static function create() :self
    {
        return new self('Комната уже занята на это время');
    }
}
