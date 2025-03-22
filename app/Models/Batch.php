<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'access_key',
        'duration'
    ];
    public function lockIfTimeExceeded()
    {
        $timeCreated = $this->created_at;
        $currentTime = Carbon::now();

        if ($timeCreated->diffInSeconds($currentTime) >= 10) {
            $this->update(['status' => 'locked']);
        }
    }


    public function students()
    {
        return $this->hasManyThrough(
            EnrolledStudent::class, 
            Result::class,          
            'batch_id',             
            'id',                   
            'id',                  
            'enrolled_student_id'   
        );
    }
}