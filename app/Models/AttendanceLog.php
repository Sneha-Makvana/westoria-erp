<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Raw export table written by Realtime Attendance Tracker software.
 * Do not add Laravel columns (id, timestamps) — Realtime reads all columns.
 */
class AttendanceLog extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'AttendanceLogs';

    protected $primaryKey = null;

    protected $fillable = [
        'EmployeeID',
        'EmployeeCode',
        'LogDateTime',
        'Direction',
        'DeviceSerialNumber',
        'DeviceipAddress',
    ];

    protected $casts = [
        'EmployeeID'   => 'integer',
        'LogDateTime'  => 'datetime',
    ];
}
