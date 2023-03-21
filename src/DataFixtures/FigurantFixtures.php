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
            $figurant->setFirstname($faker->firstName);
            $figurant->setSurname($faker->lastName);

            $manager->persist($figurant);

        }

        $manager->flush();
    }
}
