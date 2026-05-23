<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id', 'question', 'type',
        'choice_1', 'choice_2', 'choice_3', 'choice_4',
        'answer', 'status', 'professor_id'
    ];

    protected $casts = [
        'type' => 'integer',
        'status' => 'integer',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
