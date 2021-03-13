<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host_id',
        'group_id',
        'description',
        'event_time',
        'event_ending',
        'event_place'
    ];
    
    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function host(){
        return $this->belongsTo(User::class);
    }
    
    public function comments(){
        return $this->hasMany(EventComment::class);
    }
}
