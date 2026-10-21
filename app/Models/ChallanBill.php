<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallanBill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'challan_number',
        'challan_date',
        'vendor_id',
        'customer_id',
        'products',
        'remarks',
        'prepared_by',
    ];

    protected $casts = [
        'challan_date' => 'date',
        'products' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
