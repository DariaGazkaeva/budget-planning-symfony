<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private RoleRepository $roleRepository;
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->roleRepository = $roleRepository;
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

    public function save(User $user)
    {
        $role = $this->roleRepository->findByName('ROLE_USER');
        $user->addRole($role);
        $this->userRepository->save($user);
    }
}