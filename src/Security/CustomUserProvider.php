<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CustomUserProvider implements UserProviderInterface
{

    public function __construct(private readonly EntityManagerInterface $entityManager){}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $qb = $this->entityManager->createQueryBuilder();

        $query = $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :identifier')
            ->andWhere('(u.deleted IS NULL OR u.deleted = false)')
            ->setParameter('identifier', $identifier)
            ->getQuery();

        $user = $query->getOneOrNullResult();

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with identifier "%s" does not exist.', $identifier));
        }

        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException('Your account is not verified. Please wait for verification or contact us.');
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getEmail());
    }

    public function supportsClass($class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
