<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'content',
        'group_id',
        'created_at',
        'privacy',
        'shareId',
        'isDeleted'
    ];

    // Add defaults for new fields
    protected $attributes = [
        'isDeleted' => false,
        'shareId' => null
    ];

    // Make sure booleans are cast properly
    protected $casts = [
        'isDeleted' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(PostImage::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'post_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    // Add relationship to original post
    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'shareId');
    }

    // Add relationship to shared posts
    public function sharedPosts()
    {
        return $this->hasMany(Post::class, 'shareId')->where('isDeleted', false);
    }
}