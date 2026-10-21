<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation_id',
        'department_id',
        'joining_date',
        'working_location',
        'shift_time',
        'salary',
        'face_photo',
        'address',
        'delivery_address',
        'address_line2',
        'address_line3',
        'city',
        'pin_code',
        'gst_number',
        'pan_number',
        'country',
        'isDeleted',
        'supplier_since',
        'address_line2',
        'address_line3',
        'pin_code',
        'phone_2',
        'supplier_category',
        'tin_number',
        'credit_days',
        'pan_status',
        'gstin_status',
        'account_group',
        'tan_status',
        'tan_number',
        'msme_status',
        'msme_number',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'designation_id' => 'integer',
        'department_id' => 'integer',
        'joining_date' => 'date:Y-m-d',
        'salary' => 'decimal:2',
        'isDeleted' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(DesignationModel::class, 'designation_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Kolkata');
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });
    }
}
