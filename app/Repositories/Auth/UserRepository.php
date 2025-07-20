<?php 

namespace App\Repositories\Auth;

use App\Interfaces\Auth\UserRepoInterface;
use App\Models\User;

class UserRepository implements UserRepoInterface{
    public function create(array $data){
        return User::create($data);
    }
}