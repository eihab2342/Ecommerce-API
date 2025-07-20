<?php 

namespace App\Interfaces\Auth;

interface UserRepoInterface{
    public function create(array $data);
}