<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route đăng ký đã có sẵn
Route::post('/register', [AuthController::class, 'register']);

// Thêm route đăng nhập
Route::post('/login', [AuthController::class, 'login']);

// Route cập nhật ảnh đại diện
Route::post('/update-avatar', [UserController::class, 'updateAvatar']);

