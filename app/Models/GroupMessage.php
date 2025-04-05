<?php
// app/Models/GroupMessage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;
    
    protected $table = 'group_messages';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['group_chat_id', 'sender_id', 'content', 'type', 'conversation_id'];
    
    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class, 'group_chat_id');
    }
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function groupChatImage()
    {
        return $this->hasOne(GroupChatImage::class, 'group_message_id');
    }
}