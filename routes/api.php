<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'login']);  // ورود کاربران به سیستم
Route::post('/register', [UserController::class, 'store']);  // ایجاد یک کاربر جدید

Route::group(['prefix' => 'users'], function () {
    Route::get('/', [UserController::class, 'index']);  // دریافت لیست تمام کاربران
    Route::get('/{id}', [UserController::class, 'show']);  // دریافت اطلاعات یک کاربر خاص بر اساس شناسه‌اش
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);  // خروج کاربران از سیستم
    Route::get('/profile', [AuthController::class, 'showUser']);  // نمایش اطلاعات کاربر احراز هویت شده
    Route::put('/user/update', [AuthController::class, 'update']);  // به‌روزرسانی اطلاعات کاربر احراز هویت شده

    Route::group(['prefix' => 'users'], function () {
        Route::put('/{id}', [UserController::class, 'update'])->middleware(AdminMiddleware::class);  // ویرایش اطلاعات یک کاربر خاص
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware(AdminMiddleware::class);  // حذف یک کاربر خاص
    });
});
