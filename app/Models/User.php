<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;   
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'email',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password_hash',   
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class, 'user_id', 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role?->role_name === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role?->role_name === 'staff';
    }
}