<?php

namespace App\Services\Auth;

use App\Events\Auth\UserRegistered;
use App\Models\User;
use App\Repositories\Auth\UserRepository;

class UserRegisteredService
{
    public function __construct(protected UserRepository $userRepo) {}

    public function findByEmail(string $email): ?User
    {
        return $this->userRepo->findByEmail($email);
    }
    public function create(array $data)
    {
        $user = $this->userRepo->create($data);
        event(new UserRegistered($user));
        return $user;
    }
}