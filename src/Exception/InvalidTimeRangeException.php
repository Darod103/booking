<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidTimeRangeException extends \Exception
{
    public static function startNotBeforeEnd(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): self
    {
        return new self(sprintf(
            'Дата начала должна быть раньше даты окончания. Начало: %s, Окончание: %s',
            $startAt->format(\DateTimeInterface::ATOM),
            $endAt->format(\DateTimeInterface::ATOM),
        ));
    }
}
