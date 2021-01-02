<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['message'];

    public function fromUser(){
        return $this->belongsTo(User::class, "from_user_id");
    }

    public function ToUser(){
        return $this->belongsTo(User::class, "to_user_id");
    }

}
