<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route đăng ký đã có sẵn
Route::post('/register', [AuthController::class, 'register']);

// Thêm route đăng nhập
Route::post('/login', [AuthController::class, 'login']);
