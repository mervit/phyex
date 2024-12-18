<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface {

    private ObjectManager $manager;
    private Generator $faker;

    const LENGTH = 45;

    public function __construct(private UserPasswordHasherInterface $hasher){}

    public function load(ObjectManager $manager) {

        $this->faker = Factory::create();

        $this->manager = $manager;

        // Create Administrator
        $this->createUser('admin', 'password', ['ROLE_ADMIN', 'ROLE_ADMIN_USERS', 'ROLE_ADMIN_EVALUATIONS', 'ROLE_ADMIN_EXERCISES', 'ROLE_ADMIN_EXERCISE_TYPES']);

        for ($i = 0; $i < self::LENGTH; $i++) {
            $this->addReference('User_' . $i, $this->createUser($this->faker->email, 'password', []));
        }

        $this->manager->flush();
    }

    public function createUser($email, $password, $roles = []){

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles($roles);
        $user->setIsVerified(true);
        $user->setEvaluateGlobalCategories(rand(0, 1) === 1);
        $user->setBirthYear(rand(1978,2005));
        $user->setCountry($this->faker->country);
        $user->setCompletedEducationLevel($this->faker->title);
        $user->setCompletedEducationUniversityName($this->faker->name);
        $user->setCompletedEducationFacultyName($this->faker->name);
        $user->setAcademicYear(rand(1, 5));
        $user->setCourseList($this->faker->text);
        $user->setCurrentEducationLevel($this->faker->title);
        $user->setStudyProgram($this->faker->name);
        $user->setCurrentJobTitle($this->faker->title);
        $user->setFacultyName($this->faker->name);
        $user->setFavoriteMethod($this->faker->name);
        $user->setStayInTouch(rand(0, 1) === 0);
        $user->setYearsOfExperience(rand(1, 15));
        $user->setUniversityName($this->faker->name);
        $user->setFieldOfExperience($this->faker->name);
        for($c = 1; $c <= rand(0,3); ++$c) {
            $exerciseTypeCategory = $this->getReference('ExerciseTypeCategory_' . rand(0, ExerciseTypeCategoryFixtures::LENGTH - 1));
            $user->addExerciseTypeCategory($exerciseTypeCategory);
        }
        $this->manager->persist($user);
        return $user;

    }

    public function getDependencies(): array
    {
        return [
            ExerciseTypeCategoryFixtures::class
        ];
    }

}