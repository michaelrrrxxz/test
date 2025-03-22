<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';
    
    protected $fillable = [
        'enrolled_student_id',
         'batch_id',
         'raw_score_t',
         'test_ip'
        ];

        public function enrolledStudent()
        {
            return $this->belongsTo(EnrolledStudent::class, 'enrolled_student_id');
        }

        public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function information()
        {
            return $this->belongsTo(Information::class, 'enrolled_student_id');
        }
 

 
}