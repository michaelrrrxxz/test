<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

    protected $fillable = [
        'name',
     
    ];
    use HasFactory;
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}