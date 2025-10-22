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

    protected $casts = [
        'scheduled_at' => 'datetime',
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

    // Auto-generate kode unik
    public static function generateCode()
    {
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $month = date('n'); // 1-12
        $year = date('Y');

        // Ambil urutan terakhir bulan ini
        $lastShipment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $seq = $lastShipment ? (int)substr($lastShipment->code, -5) + 1 : 1;
        $seq = str_pad($seq, 5, '0', STR_PAD_LEFT);

        return "SJ/{$romanMonths[$month - 1]}/{$year}/{$seq}";
    }
}
