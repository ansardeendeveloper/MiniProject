<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'owner_id', // ADD THIS
        'registration_no',
        'model',
        'manufacturer',
        'year',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // ADD THIS RELATIONSHIP
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    // ADD THIS RELATIONSHIP
    public function services()
    {
        return $this->hasMany(ServiceRecord::class);
    }
}