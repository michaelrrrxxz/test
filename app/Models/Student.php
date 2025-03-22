<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $fillable =[
    	'id_number',
        'b_month',
        'b_date',
        'b_year',
        'name',
        'course',
        'school',
        'ex_month',	
        'ex_date',
        'ex_year',
        'gender',
        'grade',
        'age',
        'm_age',
        'tip',
        'batch_id',
        'time_el',
        'time',
        'group_abc'	

    ];



    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}