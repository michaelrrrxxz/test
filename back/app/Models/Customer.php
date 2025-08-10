<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'date_of_birth',
        'address',
        'email',
        'contact_number',
    ];
}

