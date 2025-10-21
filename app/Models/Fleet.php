<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'capacity',
        'driver_user_id',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'assigned_fleet_id');
    }

    public function trackingPoints()
    {
        return $this->hasMany(TrackingPoint::class);
    }
}
