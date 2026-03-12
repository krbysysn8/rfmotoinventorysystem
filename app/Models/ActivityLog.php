<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'subject',
        'description',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // ── Create log ────────────────────────────────────────────────
    public static function record(
        string $action,
        string $subject,
        string $description,
        ?User  $user = null,
    ): self {
        return self::create([
            'user_id'     => $user?->user_id,
            'action'      => $action,
            'subject'     => $subject,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
}