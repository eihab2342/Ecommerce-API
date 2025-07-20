<?php


namespace App\Events\Auth;

use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered 
{
    use Dispatchable, SerializesModels;


    public function __construct(public User $user) {}
}