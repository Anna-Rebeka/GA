<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupWhiteboardPost extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function group(){
        return $this->belongsTo(Group::class);
    }
}
