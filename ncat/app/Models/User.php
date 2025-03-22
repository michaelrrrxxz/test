<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;

    public function scopeInstructor($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'profile',
        'fullname',
        'department',
        'isDeleted'
    ];

    protected $attributes = [
        'isDeleted' => 0,
    ];

 
    
        // Define the scope to filter active (non-deleted) users
        public function scopeActive($query)
        {
            return $query->where('isDeleted', 0);
        }
    
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
 
}