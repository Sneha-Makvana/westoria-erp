<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_product_id',
        'target_product_id',
        'branch_id',
        'old_price',
        'new_price',
        'old_mrp',
        'new_mrp',
        'old_purchase_price',
        'new_purchase_price',
        'status',
        'resolved_by',
    ];

    public function sourceProduct()
    {
        return $this->belongsTo(Product::class, 'source_product_id');
    }

    public function targetProduct()
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }

    public function branch()
    {
        return $this->belongsTo(AccountBranch::class, 'branch_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
