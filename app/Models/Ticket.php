<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_no',
        'branch_id',
        'customer_id',
        'order_id',
        'product_id',
        'ticket_raised_in',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'remarks',
        'attachment',
        'resolved_at',
        'closed_at',
        'is_deleted',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
        'is_deleted'  => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(User::class, 'branch_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
