<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_no', 'firstname', 'lastname', 'middlename',
        'photo', 'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'username', 'student_no');
    }
}
