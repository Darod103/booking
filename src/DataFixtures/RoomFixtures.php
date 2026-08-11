<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');
        for ($i = 0; $i < 5; $i++) {
            $room = new Room();
            $room->setName("Переговорная ".$faker->city());
            $room->setCapacity($faker->numberBetween(2, 10));
            $room->setIsActive($faker->boolean(80));
            $manager->persist($room);
        }
        $manager->flush();
    }
}
