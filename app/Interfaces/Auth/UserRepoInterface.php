<?php

namespace App\Interfaces\Auth;

use App\Models\User;

interface UserRepoInterface
{
    public function EmailExist(string $email);

    public function findByEmail(string $email): ?User;
    public function create(array $data);
}