<?php

namespace App\Service;

use App\Dto\CreateRoomRequest;
use App\Entity\Room;
use App\Exception\EquipmentNotFoundException;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;


class RoomService
{

    public function __construct(
        private EquipmentRepository    $equipmentRepository,
        private EntityManagerInterface $em
    )
    {
    }

    public function create(CreateRoomRequest $request): Room
    {
        $room = new Room();
        $room->setName($request->name);
        $room->setCapacityInt($request->capacity);

        foreach ($request->equipmentIds as $equipmentId) {
            $equipment = $this->equipmentRepository->find($equipmentId);
            if (!$equipment) {
                throw  EquipmentNotFoundException::withId($equipmentId);
            }
            $room->addEquipment($equipment);
        }
        $this->em->persist($room);
        $this->em->flush();
        return $room;
    }
}
