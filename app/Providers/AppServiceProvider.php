<?php

namespace App\Providers;

use App\Interfaces\Admin\Order\AdminOrderInterface;
use App\Interfaces\Auth\UserRepoInterface;
use App\Interfaces\Order\OrderInterface;
use App\Interfaces\User\ProfileInterface;
use App\Repositories\Admin\Order\AdminOrderRepository;
use App\Repositories\Auth\UserRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\User\ProfileRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
        $bindings = [
            UserRepoInterface::class => UserRepository::class,
            OrderInterface::class => OrderRepository::class,
            ProfileInterface::class => ProfileRepository::class,
            
            //Admin
            AdminOrderInterface::class => AdminOrderRepository::class,
        ];
        foreach ($bindings as $interface => $repository) {
            $this->app->bind($interface, $repository);
        }

        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}