<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; // Khai báo đúng tên bảng

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
        'status'
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function setPasswordHashAttribute($value)
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    // Thêm các relationships với bảng relationships

    // Các mối quan hệ mà user này là người gửi lời mời
    public function sentRelationships()
    {
        return $this->hasMany(Relationship::class, 'requester_id');
    }

    // Các mối quan hệ mà user này là người nhận lời mời
    public function receivedRelationships()
    {
        return $this->hasMany(Relationship::class, 'addressee_id');
    }

    // Danh sách bạn bè (bao gồm cả hai hướng của mối quan hệ)
    public function friends()
    {
        $friendsAsRequester = $this->sentRelationships()
            ->where('status', 'accepted')
            ->with('addressee')
            ->get()
            ->pluck('addressee');

        $friendsAsAddressee = $this->receivedRelationships()
            ->where('status', 'accepted')
            ->with('requester')
            ->get()
            ->pluck('requester');

        return $friendsAsRequester->merge($friendsAsAddressee);
    }

    // Danh sách lời mời kết bạn đã gửi
    public function friendRequestsSent()
    {
        return $this->sentRelationships()
            ->where('status', 'pending')
            ->with('addressee');
    }

    // Danh sách lời mời kết bạn đã nhận
    public function friendRequestsReceived()
    {
        return $this->receivedRelationships()
            ->where('status', 'pending')
            ->with('requester');
    }

    // Danh sách người dùng đã chặn
    public function blockedUsers()
    {
        return $this->sentRelationships()
            ->where('status', 'blocked')
            ->with('addressee');
    }
}