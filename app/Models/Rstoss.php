<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rstoss extends Model
{
    use HasFactory;

    protected $table = 'rstoss';
    
    protected $fillable = [
        'raw_score_t',
        'scaled_score_t',
        'raw_score_v',
        'scaled_score_v',
        'raw_score_v',
        'scaled_score_v',
        'raw_score_nv',
        'scaled_score_nv',

        
    ];
}