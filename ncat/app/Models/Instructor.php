<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'id_number',
        'fullname',
        'username',
        'password',
        'department',
    ];

    protected $hidden = ['password'];

    
}