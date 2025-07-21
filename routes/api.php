<?php

use App\Http\Controllers\Admin\Store\CarouselImagesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Admin\Store\CategoriesController;
use App\Http\Controllers\Admin\Store\CouponController;
use App\Http\Controllers\Admin\Store\ProductsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Order\OrderController as UserOrderController;
use App\Http\Controllers\Admin\AdminOrderController;

/*
|--------------------------------------------------------------------------
| Public Routes (لا تحتاج تسجيل دخول)
|--------------------------------------------------------------------------
*/

// Route::post('signup', [AuthController::class, 'requestOtp']);
// 
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/signup', 'requestOtp');
    Route::post('/verify-otp', 'verifyOtp');
});

Route::controller(CarouselImagesController::class)->group(function () {
    Route::get('/getCarouselImages', 'getCarouselImages');
});

Route::prefix('categories')->controller(CategoriesController::class)->group(function () {
    Route::get('/', 'categories');
    Route::get('/{categoryId}/products', [ProductsController::class, 'getproductByCategoryId']);
});

Route::prefix('products')->controller(ProductsController::class)->group(function () {
    Route::get('/', 'getproducts');
    Route::get('/{id}', 'getproductById');
});

Route::get('/all-categories-products', [ProductsController::class, 'getAllCategoriesWithProducts']);


/*
|--------------------------------------------------------------------------
| User Routes (تحتاج تسجيل دخول)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'showProfile');
        Route::put('/profile', 'updateProfile');

        Route::prefix('notifications')->group(function () {
            Route::get('/', 'getNotifications');
            Route::post('/{notificationId}/mark-as-read', 'markAsRead');
            Route::post('/mark-all-as-read', 'markAllAsRead');
        });
    });

    Route::post('/applyCoupon', [CouponController::class, 'applyCoupon']);

    Route::prefix('orders')->controller(UserOrderController::class)->group(function () {
        Route::post('/', 'placeOrder');
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });
});


/*
|--------------------------------------------------------------------------
| Admin Routes (تحتاج تسجيل دخول + تحقق من isAdmin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum', 'isAdmin'])->group(function () {

    Route::prefix('products')->controller(ProductsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/add', 'store');
        Route::put('/{product}', 'update');
        Route::delete('/{product}', 'destroy');
    });

    Route::prefix('categories')->controller(CategoriesController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'getCategory');
        Route::post('/subcategories', 'storeSubCategory');
        Route::get('/subcategories/{id}', 'GetSubCategories');
        Route::delete('/subcategory/{id}', 'destroySubCategory');
        Route::put('/update/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::delete('/carouselImage/{id}', 'deleteCarouselImage');
    });

    Route::prefix('orders')->controller(AdminOrderController::class)->group(function () {
        Route::get('/', 'getAllOrders');
        Route::get('/pending', 'getPendingOrders');
        Route::get('/{id}', 'getOrder');
        Route::put('/{id}/status/update', 'updateOrderStatus');
    });

    Route::prefix('coupons')->controller(CouponController::class)->group(function () {
        Route::get('/index', 'index');
        Route::post('/store', 'store');
        Route::put('/update/{coupon}', 'update');
        Route::delete('/delete/{id}', 'destroy');
        Route::patch('/toggle/{id}', 'toggle');
    });

    Route::get('/revenue', [AdminOrderController::class, 'getRevenue']);

    Route::prefix('images')->controller(CarouselImagesController::class)->group(function () {
        Route::post('/upload', 'store');
        Route::get('/', 'index');
        Route::delete('/delete/{id}', 'destroy');
    });
});