<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;


Route::get('', function () {
    return view('welcome');
});














// require __DIR__ . '/auth.php';