<?php
declare(strict_types=1);

namespace App\Exception;

class CapacityExceededException extends \Exception
{
    public static function withLimit(int $capacity) :self
    {
        return new self("Лимит комнаты в $capacity превышен");
    }
}
