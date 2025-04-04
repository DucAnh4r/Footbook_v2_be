<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatImage extends Model
{
    use HasFactory;
    
    protected $table = 'chat_images';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['message_id', 'image_url'];
    
    public function message()
    {
        return $this->belongsTo(PrivateMessage::class, 'message_id');
    }
}