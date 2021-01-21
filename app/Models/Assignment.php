<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'author_id',
        'group_id',
        'due',
        'on_time',
        'done',
        'description',
        'created_at'
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function author(){
        return $this->belongsTo(User::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }
    
    public function comments(){
        return $this->hasMany(AssignmentsComments::class);
    }
}
