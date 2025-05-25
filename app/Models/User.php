<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'auth_provider',
        'auth_id',
        'birth_year',
        'profession',
        'avatar_url',
        'cover_photo_url',
        'address',
        'status',
        'access_token',
        'token_expires_at'
    ];

    protected $hidden = [
        'password_hash',
        'access_token', // Ẩn access token trong response
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function setPasswordHashAttribute($value)
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    // Kiểm tra token có hợp lệ không
    public function isTokenValid()
    {
        return $this->access_token && 
               $this->token_expires_at && 
               $this->token_expires_at->isFuture();
    }

    // Gửi lời mời kết bạn
    public function sentRelationships()
    {
        return $this->hasMany(Relationship::class, 'requester_id');
    }

    // Nhận lời mời kết bạn
    public function receivedRelationships()
    {
        return $this->hasMany(Relationship::class, 'addressee_id');
    }

    /**
     * Trả về danh sách bạn bè thông qua pivot, có thể dùng trong query Eloquent.
     * Lưu ý: đây là các user được bạn gửi lời mời và đã được chấp nhận
     */
    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'relationships', 'requester_id', 'addressee_id')
            ->wherePivot('status', 'accepted');
    }

    // Những người đã gửi lời mời và mình đã chấp nhận
    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'relationships', 'addressee_id', 'requester_id')
            ->wherePivot('status', 'accepted');
    }

    /**
     * Truy xuất tất cả bạn bè (cả 2 chiều)
     */
    public function friends()
    {
        return $this->friendsOfMine->merge($this->friendOf)->unique('id');
    }

    // Lời mời kết bạn đã gửi
    public function friendRequestsSent()
    {
        return $this->sentRelationships()
            ->where('status', 'pending');
    }

    // Lời mời kết bạn đã nhận
    public function friendRequestsReceived()
    {
        return $this->receivedRelationships()
            ->where('status', 'pending');
    }

    // Người dùng bị chặn
    public function blockedUsers()
    {
        return $this->sentRelationships()
            ->where('status', 'blocked');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id');
    }
}