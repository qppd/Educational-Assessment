<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    use HasFactory;

    protected $table = 'reviewers';

    protected $fillable = ['examination_id', 'professor_id', 'file', 'status'];

    public function examination()
    {
        return $this->belongsTo(Examination::class, 'examination_id');
    }
}