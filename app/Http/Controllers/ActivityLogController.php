<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = ActivityLog::with('user:user_id,full_name,username')
            ->select('id', 'user_id', 'action', 'subject', 'description', 'created_at')
            ->orderByDesc('created_at')
            ->limit(500)
            ->get()
            ->map(fn($l) => [
                'id'          => $l->id,
                'user'        => $l->user?->full_name ?? $l->user?->username ?? 'System',
                'action'      => $l->action,
                'subject'     => $l->subject,
                'description' => $l->description,
                'created_at'  => $l->created_at->format('Y-m-d H:i'),
            ]);

        return response()->json(['status' => 'success', 'data' => $logs]);
    }
}