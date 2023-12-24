<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function changePassword($email, $password)
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user !== null) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
            $this->userRepository->update($user);
        }
        return $user;
    }

    public  function findAll() {
        return $this->userRepository->findAll();
    }

    public function update(User $user)
    {
        $this->userRepository->update($user);
    }
}