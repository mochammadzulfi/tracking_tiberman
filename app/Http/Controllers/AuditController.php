<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::latest()
            ->with('user') // relasi user
            ->paginate(20);

        return view('audit.index', compact('logs'));
    }
}
