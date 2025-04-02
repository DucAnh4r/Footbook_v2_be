<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RelationshipController;

// Route đăng ký và đăng nhập
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route cập nhật thông tin người dùng
Route::post('/update-avatar', [UserController::class, 'updateAvatar']);

// Routes cho chức năng relationships
Route::prefix('relationships')->group(function () {
    // Gửi lời mời kết bạn
    Route::post('/send-request', [RelationshipController::class, 'sendFriendRequest']);

    // Chấp nhận lời mời kết bạn
    Route::post('/accept-request', [RelationshipController::class, 'acceptFriendRequest']);

    // Từ chối lời mời kết bạn
    Route::post('/decline-request', [RelationshipController::class, 'declineFriendRequest']);

    // Chặn người dùng
    Route::post('/block', [RelationshipController::class, 'blockUser']);

    // Bỏ chặn người dùng
    Route::post('/unblock', [RelationshipController::class, 'unblockUser']);

    // Hủy kết bạn
    Route::post('/unfriend', [RelationshipController::class, 'unfriend']);

    // Lấy danh sách lời mời kết bạn đã gửi
    Route::get('/sent-requests', [RelationshipController::class, 'getSentFriendRequests']);

    // Lấy danh sách lời mời kết bạn đã nhận
    Route::get('/received-requests', [RelationshipController::class, 'getReceivedFriendRequests']);

    // Lấy danh sách bạn bè
    Route::get('/friends', [RelationshipController::class, 'getFriends']);

    // Kiểm tra trạng thái mối quan hệ
    Route::get('/check-status', [RelationshipController::class, 'checkRelationshipStatus']);
});
