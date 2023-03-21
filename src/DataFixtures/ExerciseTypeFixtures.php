<?php

namespace App\DataFixtures;

use App\Entity\ExerciseType;
use App\Entity\ExerciseTypeParam;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExerciseTypeFixtures extends Fixture
{

    public function __construct(){}

    const LENGTH = 10;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < self::LENGTH; $i++) {

            $exerciseType = new ExerciseType();
            $exerciseType->setName($faker->name);

            // Params
            for ($p = 0; $p < rand(3, 5); $p++){

                $exerciseTypeParam = new ExerciseTypeParam();
                $exerciseTypeParam->setName($faker->name);
                $exerciseType->addExerciseTypeParam($exerciseTypeParam);

            }

            $manager->persist($exerciseType);

        }

        $manager->flush();
    }
}
