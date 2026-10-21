<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerDeposit extends Model
{
    protected $table = 'customer_deposits';

    protected $fillable = [
        'customer_id',
        'branch_id',
        'order_id',
        'entry_type',
        'amount',
        'balance_after',
        'source',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Current reusable deposit balance for a customer (optionally branch-scoped).
     */
    public static function balanceFor(int $customerId, ?int $branchId = null): float
    {
        $query = static::query()->where('customer_id', $customerId);
        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        $credits = (clone $query)->where('entry_type', 'credit')->sum('amount');
        $debits = (clone $query)->where('entry_type', 'debit')->sum('amount');

        return round(max(0, (float) $credits - (float) $debits), 2);
    }

    /**
     * Credit deposit (e.g. exchange surplus kept with shop).
     */
    public static function credit(
        int $customerId,
        float $amount,
        ?int $branchId = null,
        ?int $orderId = null,
        string $source = 'exchange_surplus',
        ?string $notes = null,
        ?int $createdBy = null
    ): self {
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit credit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($customerId, $amount, $branchId, $orderId, $source, $notes, $createdBy) {
            $balance = static::balanceFor($customerId, $branchId) + $amount;

            return static::create([
                'customer_id' => $customerId,
                'branch_id' => $branchId,
                'order_id' => $orderId,
                'entry_type' => 'credit',
                'amount' => $amount,
                'balance_after' => $balance,
                'source' => $source,
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);
        });
    }

    /**
     * Debit / apply deposit toward a bill.
     */
    public static function debit(
        int $customerId,
        float $amount,
        ?int $branchId = null,
        ?int $orderId = null,
        string $source = 'bill_payment',
        ?string $notes = null,
        ?int $createdBy = null
    ): self {
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Deposit debit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($customerId, $amount, $branchId, $orderId, $source, $notes, $createdBy) {
            $available = static::balanceFor($customerId, $branchId);
            if ($amount > $available + 0.009) {
                throw new \RuntimeException('Insufficient customer deposit balance.');
            }

            $balance = round($available - $amount, 2);

            return static::create([
                'customer_id' => $customerId,
                'branch_id' => $branchId,
                'order_id' => $orderId,
                'entry_type' => 'debit',
                'amount' => $amount,
                'balance_after' => $balance,
                'source' => $source,
                'notes' => $notes,
                'created_by' => $createdBy,
            ]);
        });
    }
}
