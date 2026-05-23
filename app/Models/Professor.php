<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no', 'surname', 'firstname',
        'designation', 'email', 'contact', 'photo', 'password', 'status'
    ];
}
