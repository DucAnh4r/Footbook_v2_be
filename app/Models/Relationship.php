<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    use HasFactory;

    protected $table = 'relationships';
    public $timestamps = false; // Nếu bạn không sử dụng created_at và updated_at trong bảng

    protected $fillable = [
        'requester_id',
        'addressee_id',
        'status'
    ];

    // Quan hệ với user gửi yêu cầu
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // Quan hệ với user nhận yêu cầu
    public function addressee()
    {
        return $this->belongsTo(User::class, 'addressee_id');
    }
}