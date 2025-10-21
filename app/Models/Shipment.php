<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'barcode_data',
        'customer_name',
        'origin_address',
        'destination_address',
        'weight',
        'volume',
        'status',
        'assigned_fleet_id',
        'assigned_driver_id',
        'scheduled_at',
        'created_by_user_id'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class, 'assigned_fleet_id');
    }

    public function trackingPoints()
    {
        return $this->hasMany(TrackingPoint::class);
    }

    public function deliveryProofs()
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
