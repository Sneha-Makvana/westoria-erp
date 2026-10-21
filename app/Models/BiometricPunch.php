<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricPunch extends Model
{
    protected $fillable = [
        'device_serial',
        'device_user_id',
        'user_id',
        'employee_name',
        'punch_time',
        'punch_date',
        'punch_type',
        'verify_mode',
        'device_state',
        'device_ip',
        'source',
        'raw_payload',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'punch_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
