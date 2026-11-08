<?php
declare(strict_types=1);

namespace App\Dto;

use App\ValueObject\BookingParticipants;
use Symfony\Component\Validator\Constraints as Assert;

class CreateBookingRequest
{
    public function __construct(
        #[Assert\NotBlank(normalizer: 'trim')]
        #[Assert\Uuid]
        public string $roomId,
        #[Assert\NotNull]
        public \DateTimeImmutable $startAt,
        #[Assert\NotNull]
        public \DateTimeImmutable $endAt,
        #[Assert\NotBlank(normalizer: 'trim')]
        public string $organizerId,
        #[Assert\Range(min: BookingParticipants::MIN, max: BookingParticipants::MAX)]
        public int $participants,
    ) {
    }
}
