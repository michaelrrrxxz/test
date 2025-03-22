<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable =[
        'city_id',
        'school_name',
   
    ];
    use HasFactory;

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}