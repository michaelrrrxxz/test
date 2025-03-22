<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'user_agent', 'login_at', 'logout_at'
    ];


    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}