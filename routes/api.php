<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


// Route đăng ký
Route::post('/register', [AuthController::class, 'register']);

// Route đăng nhập
Route::post('/login', [AuthController::class, 'login']);

// Route cập nhật ảnh đại diện
Route::post('/update-avatar', [UserController::class, 'updateAvatar']);

// Route cập nhật thông tin tài khoản
Route::post('/update-profile', [UserController::class, 'updateProfile']);

Route::post('/change-password', [UserController::class, 'changePassword']);

Route::get('/user/{id}', [UserController::class, 'getProfile']);


