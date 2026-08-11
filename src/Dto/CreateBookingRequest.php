<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateBookingRequest
{
    function __construct(
        #[Assert\NotBlank]
        public string             $roomId,
        #[Assert\NotNull]
        public \DateTimeImmutable $startAt,
        #[Assert\NotNull]
        public \DateTimeImmutable $endAt,
        #[Assert\NotBlank]
        public string             $organizerId,
        #[Assert\Positive]
        public int                $participants

    )
    {
    }

}
