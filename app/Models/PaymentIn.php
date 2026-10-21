<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentIn extends Model
{
    protected $table = 'payment_ins';

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'branch_id',
        'amount',
        'payment_method',
        'bank_id',
        'payment_date',
        'settle_old_bills',
        'settled_amount',
        'deposit_amount',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settled_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'settle_old_bills' => 'boolean',
        'payment_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bank()
    {
        return $this->belongsTo(BankMaster::class, 'bank_id');
    }
}
