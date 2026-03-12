<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifyAction extends Model
{
    protected $primaryKey = 'verify_id';

    protected $fillable = [
        'action_type', 'reference_type', 'reference_id',
        'requested_by', 'verified_by', 'status', 'notes',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }
}
