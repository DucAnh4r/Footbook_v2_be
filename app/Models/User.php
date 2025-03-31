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
}
