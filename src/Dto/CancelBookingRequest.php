<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CancelBookingRequest
{
    public function __construct(
        #[Assert\Uuid]
        public string $bookingId
    ) {
    }
}