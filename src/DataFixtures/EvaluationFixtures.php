<?php

namespace App\DataFixtures;

use App\Entity\Evaluation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EvaluationFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(){}

    const LENGTH = 200;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < self::LENGTH; $i++) {

            $evaluation = new Evaluation();
            $evaluation->setComment($faker->text);
            $evaluation->setExercise($this->getReference('Exercise_'. rand(1, 10)));
            $evaluation->setUser($this->getReference('User_'. rand(0, UserFixtures::LENGTH -1)));
            $evaluation->setCreated($faker->dateTimeBetween('-1 month', 'now'));

            $manager->persist($evaluation);

        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ExerciseFixtures::class,
            UserFixtures::class
        ];
    }
}
