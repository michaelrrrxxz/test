<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $table = 'student_answers';
    protected $fillable = [
    'student_id', 'question_id', 'selected_option', 'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}