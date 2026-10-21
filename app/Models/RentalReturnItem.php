<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalReturnItem extends Model
{
    use HasFactory;

    protected $table = 'rental_return_items';

    protected $fillable = [
        'rental_return_id',
        'order_item_id',
        'product_id',
        'variant_id',
        'qty_returned',
        'condition',
        'damage_charge',
        'late_fee',
    ];

    public function rentalReturn()
    {
        return $this->belongsTo(RentalReturn::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
