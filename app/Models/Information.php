<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Information extends Model
{


    protected $fillable = [
        'student_id',
        'address',
        'birth_date',
        'region_id',
        'city_id',
        'province_id',
        'school_id',
        'group_abc'
        
    ];
    use HasFactory;

    public function students()
    {
        return $this->belongsTo(EnrolledStudent::class, 'student_id');
    }

    // Through the student relationship, you can access the result
    public function results()
    {
        return $this->hasOneThrough(Result::class, EnrolledStudent::class, 'id', 'enrolled_student_id', 'student_id', 'id');
    }


    public function region()
{
    return $this->belongsTo(Region::class);
}

public function city()
{
    return $this->belongsTo(City::class);
}

public function province()
{
    return $this->belongsTo(Province::class);
}

public function school()
{
    return $this->belongsTo(School::class);
}
}