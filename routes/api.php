<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarouselImagesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

/*
|--------------------------------------------------------------------------
| Public Routes (لا تحتاج تسجيل دخول)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/sign-up', [AuthController::class, 'SendOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('/getCarouselImages', [CarouselImagesController::class, 'getCarouselImages']);
Route::get('/categories', [CategoriesController::class, 'categories']);
Route::get('/products', [ProductsController::class, 'getproducts']);
Route::get('/products/{id}', [ProductsController::class, 'getproductById']);
Route::get('/categories/{categoryId}/products', [ProductsController::class, 'getproductByCategoryId']);
Route::get('/all-categories-products', [ProductsController::class, 'getAllCategoriesWithProducts']);



/*
|--------------------------------------------------------------------------
| Protected Routes (تحتاج تسجيل دخول للمستخدم العادي)
|--------------------------------------------------------------------------
*/
Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum'])->group(function () {
    // استعراض بيانات المستخدم
    Route::get('/user', function (Request $request) {
        return response()->json([
            'message' => 'Authenticated',
            'user' => $request->user()
        ]);
    });

    // البروفايل
    Route::post('/profile', [UserController::class, 'getProfile']);
    Route::put('/update/profile', [UserController::class, 'updateProfile']);

    // الكوبونات
    Route::post('/applyCoupon', [CouponController::class, 'applyCoupon']);

    // الطلبات
    Route::post('/orders', [OrderController::class, 'placeOrder']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // الاشعارات
    Route::get('/notifications', [UserController::class, 'getNotifications']);
    Route::post('/notifications/{notificationId}/mark-as-read', [UserController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [UserController::class, 'markAllAsRead']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (تحتاج تسجيل دخول + role = admin)
|--------------------------------------------------------------------------
*/


Route::middleware([EnsureFrontendRequestsAreStateful::class, 'auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {
    //
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'index']);
        Route::post('/add', [ProductsController::class, 'store']);
        Route::put('/{product}', [ProductsController::class, 'update']);
        Route::delete('/{product}', [ProductsController::class, 'destroy']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'index']);
        Route::post('/', [CategoriesController::class, 'store']);
        Route::get('/{id}', [CategoriesController::class, 'getCategory']);
        Route::post('/subcategories', [CategoriesController::class, 'storeSubCategory']);
        Route::get('/subcategories/{id}', [CategoriesController::class, 'GetSubCategories']);
        Route::delete('/subcategory/{id}', [CategoriesController::class, 'destroySubCategory']); // خلي دي قبل /{id}
        Route::put('update/{id}', [CategoriesController::class, 'update']);
        Route::delete('/{id}', [CategoriesController::class, 'destroy']);
        Route::delete('/carouselImage/{id}', [CategoriesController::class, 'deleteCarouselImage']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'getAllOrders']);
        Route::get('/pending', [OrderController::class, 'getPendingOrders']);
        Route::get('/{id}', [OrderController::class, 'getOrder']);
        Route::put('/{id}/status/update', [OrderController::class, 'updateOrderStatus']);
    });


    Route::prefix('coupons')->group(function () {
        Route::get('index', [CouponController::class, 'index']);
        Route::post('store', [CouponController::class, 'store']);
        Route::put('update/{id}', [CouponController::class, 'update']);     // للتعديل
        Route::delete('delete/{id}', [CouponController::class, 'destroy']); // للحذف
        Route::patch('toggle/{id}', [CouponController::class, 'toggle']);   // لتغيير حالة التفعيل
    });

    Route::get('/revenue', [OrderController::class, 'getRevenue']);
    // 
    Route::post('/upload-images', [CarouselImagesController::class, 'store']);
    Route::get('/images', [CarouselImagesController::class, 'index']);
    Route::delete('/images/delete/{id}', [CarouselImagesController::class, 'destroy']);
});
