<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'avatar_url', 
        'created_at'
    ];

    // Relationships
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'group_id');
    }
}