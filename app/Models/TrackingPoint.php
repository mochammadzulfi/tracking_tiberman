<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'fleet_id',
        'driver_id',
        'lat',
        'lng',
        'source',
        'ip_address',
        'ip_geo',
        'device_info',
        'is_ip_mismatch',
        'note'
    ];

    protected $casts = [
        'ip_geo' => 'array',
        'device_info' => 'array',
        'is_ip_mismatch' => 'boolean'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
