<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    use HasFactory;


    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function latestMessage(){
        return $this->hasOne(Message::class)->latest();
    }

    public function betweenTwoUsers(User $user1, User $user2){
        if(count($this->users) != 2){ return null; }
        if ($this->users->contains($user1) && $this->users->contains($user2)){ return $this; }
        return null;
    }
    
    public function hasUser($user){
        return $this->users->contains($user);
    }
    

}
