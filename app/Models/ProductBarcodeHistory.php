<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBarcodeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'old_barcode',
        'new_barcode',
        'old_price',
        'new_price',
        'old_mrp',
        'new_mrp',
        'old_batch_no',
        'new_batch_no',
        'old_label_code',
        'new_label_code',
        'old_label_reference',
        'new_label_reference',
        'old_barcode_details',
        'new_barcode_details',
        'old_tax_rate',
        'new_tax_rate',
        'old_sale_discount',
        'new_sale_discount',
        'old_discount_type',
        'new_discount_type',
        'old_minimum_stock_qty',
        'new_minimum_stock_qty',
        'changed_by',
    ];

    protected $casts = [
        'old_barcode_details' => 'array',
        'new_barcode_details' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
