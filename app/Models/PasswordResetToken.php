<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetToken extends Model
{
    protected $table      = 'rfmoto_password_resets';
    public    $timestamps = false;

    protected $fillable = [
        'user_id',
        'token',
        'type',
        'used',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'used'       => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }
}