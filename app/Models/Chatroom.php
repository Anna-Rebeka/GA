<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    use HasFactory;

    
    public function userA(){
        return $this->belongsTo(User::class,'user_a_id');
    }

    public function userB(){
        return $this->belongsTo(User::class,'user_b_id');
    }

    public function users(){
        return $this->userA->merge($this->userB);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function latestMessage(){
        return $this->messages()->orderBy('created_at', 'DESC')->limit(1);
    }


    

}
