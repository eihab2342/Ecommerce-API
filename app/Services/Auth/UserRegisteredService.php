<?php

namespace App\Services\Auth;

use App\Events\Auth\UserRegistered;
use App\Models\User;
use App\Repositories\Auth\UserRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserRegisteredService
{
    public function __construct(protected UserRepository $userRepo) {}

    public function EmailExist(string $email){
        return $this->userRepo->EmailExist($email);
    }
    public function checkEmailOrFail(string $email): void
    {
        if ($this->EmailExist($email)) {
            throw new HttpException(409,'User already registered with this email.');
        }
    }


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