<?php

namespace App\DataFixtures;

use App\Entity\Figurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FigurantFixtures extends Fixture
{

    public function __construct(){}

    const LENGTH = 10;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < self::LENGTH; $i++) {

            $figurant = new Figurant();
            $figurant->setNickname($faker->firstName);
            $figurant->setGender(rand(0, 1) === 0 ? 'Male': 'Female');
            $figurant->setHeight(rand(154, 190));
            $figurant->setWeight(rand(52, 102));
            $figurant->setActiveHoursPerWeek(rand(2,7));
            $figurant->setSportHoursPerWeek(rand(2,7));
            $figurant->setAge(rand(18, 75));
            $figurant->setOccupation($faker->name);
            $figurant->setSittingTimePerDay(rand(1, 16));
            $figurant->setStretchingFrequency(rand(1, 16));
            $figurant->setPublicVideoConfirmation(rand(0, 1) === 0);

            $manager->persist($figurant);

        }

        $manager->flush();
    }
}
