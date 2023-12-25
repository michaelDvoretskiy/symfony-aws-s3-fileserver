<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $em
    ) {}

    public function createUser($userData)
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userData->password
        );

        $user->setPassword($hashedPassword);
        $user->setEmail($userData->email);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function refreshUserToken($user)
    {
        $token = bin2hex(random_bytes(64));
        $user->setToken($token);
        $this->em->flush();

        return $token;
    }
}