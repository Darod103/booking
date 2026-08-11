<?php

namespace App\Exception;



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_BAD_REQUEST)]
class EquipmentNotFoundException extends \Exception
{
    public static function withId(int $id): self
    {
        return new self("Не ненайдено оборудования с таким id $id");
    }

}
