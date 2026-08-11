<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $staff = ['Телевизор', 'Проектор', 'Доска', 'Система видеоконференций','Игровая приставка'];
        foreach ($staff as $item) {
            $equipment = new Equipment();
            $equipment->setName($item);
            $manager->persist($equipment);
        }

        $manager->flush();
    }
}
