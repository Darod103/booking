<?php

namespace App\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
class CapacityExceededException extends \Exception
{
    public static function withLimit(int $capacity) :self
    {
        return new self("Лимит комнаты в $capacity превышен");
    }
}
