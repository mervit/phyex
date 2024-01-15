<?php

namespace App\DataFixtures;

use App\Entity\ExerciseTypeCategory;
use App\Entity\Figurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ExerciseTypeCategoryFixtures extends Fixture
{

    public function __construct(){}

    const LENGTH = 5;

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();

        for ($i = 0; $i < self::LENGTH; $i++) {

            $category = new ExerciseTypeCategory();
            $category->setName($faker->word);
            $category->setDeleted(false);
            $category->setGlobal(rand(0,1) === 1);

            $manager->persist($category);

            $this->setReference('ExerciseTypeCategory_' . $i, $category);

        }

        $manager->flush();
    }
}
