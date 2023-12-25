<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterDto extends AbstractJsonRequest
{
    #[Assert\NotBlank(message: 'email parameter should be passed')]
    public readonly string $email;
    #[Assert\NotBlank(message: 'password parameter should be passed')]
    public readonly string $password;

    #[Assert\IsTrue(message: 'Email is not unique')]
    private function isUniqueEmail()
    {
        if (!($this->email ?? false)) {
            return true;
        }

        $user = $this->em->getRepository(User::class)->findOneBy(["email" => $this->email]);
        if($user) {
            return  $this->email;
        }
        return true;
    }

    #[Assert\IsTrue(message: 'Minimum length of 8 characters. Should contain: at least one uppercase letter, at least one lowercase letter, at least one digit')]
    private function isPasswordComplexity()
    {
        if (!($this->password ?? false)) {
            return true;
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $this->password)) {
            return false;
        }
        return true;
    }
}