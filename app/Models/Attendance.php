<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected static ?string $resolvedTable = null;

    public static function resolvedTable(): string
    {
        if (static::$resolvedTable) {
            return static::$resolvedTable;
        }

        if (Schema::hasTable('attendance')) {
            return static::$resolvedTable = 'attendance';
        }

        if (Schema::hasTable('attendances')) {
            return static::$resolvedTable = 'attendances';
        }

        return static::$resolvedTable = (new static())->table ?: 'attendance';
    }

    public function getTable()
    {
        return static::resolvedTable();
    }

    protected $fillable = [
        'branch_id',
        'user_id',
        'date',
        'check_in_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_location_name',
        'check_out_time',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_location_name',
        'meal_break',
        'work_hours',
        'status',
        'reason',
        'description',
        'extraday',
        'overtime',
        'checkin_method',
        'is_late',
        'late_minutes',
        'location',
        'check_in_ip',
        'check_out_ip',
        'late_duration'
    ];
}

