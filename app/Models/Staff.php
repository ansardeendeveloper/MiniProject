<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'email',
        'address',
        'date_of_birth',
        'age',
        'phone',
        'role',
        'password',
        'image',
    ];

    protected $hidden = ['password'];
}
