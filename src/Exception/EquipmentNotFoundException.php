<?php
declare(strict_types=1);

namespace App\Exception;

class EquipmentNotFoundException extends \Exception
{
    public static function withId(int $id): self
    {
        return new self("Не ненайдено оборудования с таким id $id");
    }

}
