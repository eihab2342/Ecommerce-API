<?php  

namespace App\Services\Auth;

use App\Events\Auth\UserRegistered;
use App\Repositories\Auth\UserRepository;

class UserRegisteredService{
    public function __construct(protected UserRepository $userRepo){}

    public function create(array $data){
        $user = $this->userRepo->create($data);
        event(new UserRegistered($user));
    }
    
}