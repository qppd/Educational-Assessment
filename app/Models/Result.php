<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'examination_id', 'score', 'remarks', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }
}
