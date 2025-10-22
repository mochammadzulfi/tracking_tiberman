<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditHelper
{
    public static function log($shipmentId, $action, $meta = [])
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'shipment_id' => $shipmentId,
            'action' => $action,
            'meta' => json_encode($meta),
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
