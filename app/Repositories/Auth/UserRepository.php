<?php

namespace App\Repositories\Auth;

use App\Interfaces\Auth\UserRepoInterface;
use App\Models\User;

class UserRepository implements UserRepoInterface
{

    public function EmailExist(string $email)
    {
        return User::where('email', $email)->exists();
    }
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    public function create(array $data)
    {
        return User::create($data);
    }
}