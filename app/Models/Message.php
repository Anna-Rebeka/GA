<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GregoryDuckworth\Encryptable\EncryptableTrait;

class Message extends Model
{
    use HasFactory;
    use EncryptableTrait;

    protected $guarded = [];
    protected $encryptable = [
		'text'
	];
    
    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function chatroom(){
        return $this->belongsTo(Chatroom::class);
    }

}
