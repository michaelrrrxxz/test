<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
    protected $table = 'tbl_trial_qst';
    protected $fillable = [
    'testtype', 'question', 'ch1', 'ch2','ch3','ch4','ch5','qtype'
    ];

    use HasFactory;
}