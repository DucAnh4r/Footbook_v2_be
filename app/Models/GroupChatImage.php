<?php
// app/Models/GroupChatImage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChatImage extends Model
{
    use HasFactory;
    
    protected $table = 'group_chat_images';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['group_message_id', 'image_url'];
    
    public function groupMessage()
    {
        return $this->belongsTo(GroupMessage::class, 'group_message_id');
    }
}