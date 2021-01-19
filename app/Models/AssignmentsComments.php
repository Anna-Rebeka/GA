<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentsComments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignment_id',
        'text',
        'created_at'
    ];

    public function assignment(){
        return $this->belongsTo(Assignment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
