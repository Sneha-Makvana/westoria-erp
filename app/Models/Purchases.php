<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'item',
        'product_variant_id',
        'quantity',
        'product_gst_details',
        'product_gst_total',
        'price',
        'selling_rate_price',
        'purchase_status',
        'payment_status',
        'amount_total',
        'discount_amount',
        'discount_percent',
        'free_quantity',
        'imei_no',
        'activation_date',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'net_amount',
        'vendor_id',
        'invoice_id',
        'isDeleted',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'product_gst_details' => 'array',
        'activation_date' => 'date:Y-m-d',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'item', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    public function invoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'invoice_id'); // Adjust foreign key as per your schema
    }
      public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
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
