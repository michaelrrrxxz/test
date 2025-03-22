<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolledStudent extends Model
{

    protected $table = 'enrolled_students';
    
    protected $fillable = [
        'id_number',
        'name',
        'course',
        'department',
       
        'gender',
      
        'exam_year',
    ];




    public function results()
    {
        return $this->hasMany(Result::class, 'enrolled_student_id');
    }
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }
    public function information()
    {
        return $this->hasOne(Information::class, 'student_id');
    }
    
    
}