<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    use HasFactory;
    
    protected $table = 'private_messages';
    public $timestamps = false; // Vì DB không có updated_at

    protected $fillable = ['conversation_id', 'sender_id', 'receiver_id', 'content', 'type'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
    
    public function chatImage()
    {
        return $this->hasOne(ChatImage::class, 'message_id');
    }
}