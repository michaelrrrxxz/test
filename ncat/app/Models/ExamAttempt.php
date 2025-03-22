<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    // Specify which columns are fillable
    protected $fillable = [
        'student_id',
        'start_time',
        'end_time',
        
    ];

    // Define the relationship to the EnrolledStudent model
    public function student()
    {
        return $this->belongsTo(EnrolledStudent::class, 'student_id');
    }
    public function results()
    {
        return $this->belongsTo(Result::class, 'student_id');
    }
}