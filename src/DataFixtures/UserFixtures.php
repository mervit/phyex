<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture {

    private ObjectManager $manager;

    public function __construct(private UserPasswordHasherInterface $hasher){}

    public function load(ObjectManager $manager) {

        $this->manager = $manager;

        // Create Administrator
        $this->createUser('admin', 'password', ['ROLE_ADMIN']);

        $this->manager->flush();
    }

    public function createUser($email, $password, $roles = []){

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles($roles);
        $this->manager->persist($user);

    }

}