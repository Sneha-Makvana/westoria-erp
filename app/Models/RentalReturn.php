<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalReturn extends Model
{
    use HasFactory;

    protected $table = 'rental_returns';

    protected $fillable = [
        'order_id',
        'branch_id',
        'returned_by',
        'return_date',
        'total_late_fee',
        'total_damage_charge',
        'final_settlement_amount',
        'payment_method',
        'refund_amount',
        'remarks',
        'is_full_return',
        'deposit_settled',
        'deposit_amount_applied',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(RentalReturnItem::class);
    }
}
