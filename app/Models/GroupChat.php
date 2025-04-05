<?php
// app/Models/GroupChat.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    use HasFactory;
    
    protected $table = 'group_chat';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = ['name', 'avatar_url'];
    
    public function members()
    {
        return $this->hasMany(GroupChatMember::class, 'group_chat_id');
    }
    
    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_chat_id');
    }
}