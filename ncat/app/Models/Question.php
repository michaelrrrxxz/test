<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function scopeActive($query)
    {
        return $query->where('isDeleted', 0);
    }
    
    protected $fillable = [
        'test_type', 'question', 'option_a', 'option_b', 'option_c', 'option_d','option_e', 'option_correct', 'ctype','isDeleted'
    ];

    protected $attributes = [
        'isDeleted' => 0,
    ];

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}