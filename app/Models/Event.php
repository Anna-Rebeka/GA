<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author_id',
        'group_id',
        'description',
        'event_time',
        'event_place'
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function author(){
        return $this->belongsTo(User::class);
    }
    

}
