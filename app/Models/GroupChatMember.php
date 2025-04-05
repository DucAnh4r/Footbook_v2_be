<?php
// app/Models/GroupChatMember.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChatMember extends Model
{
    use HasFactory;
    
    protected $table = 'group_chat_members';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['group_chat_id', 'user_id', 'joined_at'];
    
    public function groupChat()
    {
        return $this->belongsTo(GroupChat::class, 'group_chat_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}