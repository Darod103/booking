<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateRoomRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\Positive]
        public int $capacity,
        #[Assert\All([
            new Assert\Type('int'),
            new Assert\Positive(),
        ])]
        public array $equipmentIds = []
    ) {
    }
}
