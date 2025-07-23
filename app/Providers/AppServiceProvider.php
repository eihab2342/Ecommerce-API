<?php

namespace App\Providers;

use App\Interfaces\Admin\Order\AdminOrderInterface;
use App\Interfaces\Auth\UserRepoInterface;
use App\Interfaces\Order\OrderInterface;
use App\Interfaces\Store\CategoryInterface;
use App\Interfaces\Store\ImageInterface;
use App\Interfaces\Store\ProductInterface;
use App\Interfaces\User\ProfileInterface;
use App\Repositories\Admin\Order\AdminOrderRepository;
use App\Repositories\Auth\UserRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Store\CategoryRepository;
use App\Repositories\Store\ImageRepository;
use App\Repositories\Store\ProductRepository;
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
            // *********User*********Auth*********
            UserRepoInterface::class => UserRepository::class,
            ProfileInterface::class => ProfileRepository::class,

            // *********Order*********AdminOrder*********
            OrderInterface::class => OrderRepository::class,


            // *********Admin*********Admin*********
            CategoryInterface::class => CategoryRepository::class,
            ImageInterface::class => ImageRepository::class,
            ProductInterface::class => ProductRepository::class,

            // *********Admin*********Admin*********
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