<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\GroupController;

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

// Routes for posts
Route::prefix('posts')->group(function () {
    // Create post
    Route::post('/create', [PostController::class, 'createPost']);

    // Get post details
    Route::get('/{id}', [PostController::class, 'getPost']);

    // Update post
    Route::put('/update', [PostController::class, 'updatePost']);

    // Delete post
    Route::delete('/delete', [PostController::class, 'deletePost']);

    // Get user's posts
    Route::get('/user/posts', [PostController::class, 'getUserPosts']);

    // Get feed posts
    Route::get('/feed', [PostController::class, 'getFeedPosts']);
});

// Routes for comments
Route::prefix('comments')->group(function () {
    // Add comment
    Route::post('/add', [CommentController::class, 'addComment']);

    // Update comment
    Route::put('/update', [CommentController::class, 'updateComment']);

    // Delete comment
    Route::delete('/delete', [CommentController::class, 'deleteComment']);

    // Get post comments
    Route::get('/post/{post_id}', [CommentController::class, 'getPostComments']);
});

// Routes for reactions
Route::prefix('reactions')->group(function () {
    // React to post
    Route::post('/react', [ReactionController::class, 'reactToPost']);

    // Remove reaction
    Route::delete('/remove', [ReactionController::class, 'removeReaction']);

    // Get post reactions
    Route::get('/post/{post_id}', [ReactionController::class, 'getPostReactions']);
});

// Routes for groups
Route::prefix('groups')->group(function () {
    // Create group
    Route::post('/create', [GroupController::class, 'createGroup']);

    // Get group details
    Route::get('/{id}', [GroupController::class, 'getGroup']);

    // Update group information
    Route::put('/update', [GroupController::class, 'updateGroup']);

    // Delete group
    Route::delete('/delete', [GroupController::class, 'deleteGroup']);

    // Add member to group
    Route::post('/members/add', [GroupController::class, 'addMember']);

    // Remove member from group
    Route::delete('/members/remove', [GroupController::class, 'removeMember']);

    // Get group members
    Route::get('/{group_id}/members', [GroupController::class, 'getGroupMembers']);

    // Get group posts (user_id is optional)
    Route::get('/{group_id}/posts/{user_id?}', [GroupController::class, 'getGroupPosts']);

    // Get user's groups
    Route::get('/user/{user_id}', [GroupController::class, 'getUserGroups']);

    // Search for groups
    Route::get('/search/{query}', [GroupController::class, 'searchGroups']);
});

Route::prefix('chat')->group(function () {
    Route::post('/send', [ChatController::class, 'sendMessage']); // Gửi tin nhắn
    Route::get('/conversation/{id}', [ChatController::class, 'getMessages']); // Lấy tin nhắn
    Route::get('/user/{id}/conversations', [ChatController::class, 'getUserConversations']); // Lấy danh sách cuộc trò chuyện
});
