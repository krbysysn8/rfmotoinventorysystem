<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table        = 'users';
    protected $primaryKey   = 'user_id';
    public    $incrementing = true;
    protected $keyType      = 'int';

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'email',
        'role_id',
        'status',
        'last_login',      // ← was missing; AuthController calls $user->update(['last_login' => now()])
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    // ── Required by Sanctum / Laravel Auth ───────────────────
    // Maps the non-standard column name so Hash::check() works
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // ── Relationships ─────────────────────────────────────────
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class, 'user_id', 'user_id');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role?->role_name === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role?->role_name === 'staff';
    }
}