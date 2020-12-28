<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'admin_id'
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function hasUser($user) {
        return $this->users->contains($user);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }

    public function assignments(){
        return $this->hasMany(Assignment::class);
    }
}

