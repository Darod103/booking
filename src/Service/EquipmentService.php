<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class EquipmentService
{
    public function __construct(
        private EquipmentRepository $equipmentRepository,
    ){}

    public function getAllEquipments(): array
    {
        return $this->equipmentRepository->findAll();
    }

}
