<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $table      = 'login_logs';
    protected $primaryKey = 'log_id';
    public    $timestamps = false;

    protected $fillable = [
        'username',
        'user_id',
        'role_attempted',
        'success',
        'ip_address',
        'user_agent',
        'fail_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}