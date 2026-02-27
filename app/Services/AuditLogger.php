<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log($action, $recordId = null, $details = null)
    {
        $user = Auth::user();

        AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'role' => $user ? $user->getRoleNames()->first() ?? 'Unknown' : 'Guest',
            'action' => $action,
            'ip_address' => Request::ip(),
            'affected_record_id' => $recordId,
            'details' => is_array($details) ? json_encode($details) : $details,
        ]);
    }
}
