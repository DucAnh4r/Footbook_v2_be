<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasFactory;

    protected $table = 'post_images';
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'image_url',
        'created_at',
    ];

    // Relationships
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}