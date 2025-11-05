<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    protected $fillable = [
        'staff_id',
        'vehicle_id',
        'owner_id',
        'service_start_datetime',
        'service_end_datetime',
        'service_types',
        'amount',
        'status',
        'job_id',
        'km_run',
        'payment_status',
    ];

    protected $casts = [
        'service_types' => 'array',
        'service_start_datetime' => 'datetime',
        'service_end_datetime' => 'datetime',
        'payment_status' => 'string',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}