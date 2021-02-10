<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentsFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assignment_file',
        'file_name',
        'assignment_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function assignment(){
        return $this->belongsTo(Assignment::class);
    }
}
