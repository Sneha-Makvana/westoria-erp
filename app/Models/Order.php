<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = [
        'order_number',
        'import_sn',
        'import_party_name',
        'import_doc_no',
        'import_doc_date',
        'import_doc_value',
        'import_financier',
        'import_status',
        'import_do_status',
        'import_source',
        'shipping',
        'other_charges',
        'exchange_material_id',
        'exchange_type_id',
        'exchange_price',
        'exchange_weight',
        'exchange_amount',
        'exchange_surplus',
        'exchange_surplus_action',
        'exchange_details',
        'tds_percentage',
        'tds_amount',
        'deposit_amount',
        'customer_deposit_applied',
        'user_id',
        'discount',
        'tax_id',
        'gst_option',
        'branch_id',
        'created_by',
        'staff_id',
        'order_type',
        'total_amount',
        'remaining_amount',
        'emi_down_payment',
        'emi_loan_amount',
        'emi_interest_rate',
        'emi_tenure',
        'emi_monthly_amount',
        'emi_aadhar_number',
        'emi_do_id',
        'emi_pan_number',
        'emi_guarantor_name',
        'payment_status',
        'delivery_status',
        'payment_method',
        'order_invoice',
        'quotation_status',
        'approved_status',
        'remarks',
        'custom_qr_code',
        'isDeleted',
        'rental_status',
        'return_status',
        'created_at',
        'updated_at',
    ];  

    protected $casts = [
        'tax_id' => 'array',
        'import_doc_date' => 'date:Y-m-d',
        'import_doc_value' => 'decimal:2',
        'tds_percentage' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'emi_down_payment' => 'decimal:2',
        'emi_loan_amount' => 'decimal:2',
        'emi_interest_rate' => 'decimal:2',
        'emi_monthly_amount' => 'decimal:2',
        'other_charges' => 'array',
        'exchange_price' => 'decimal:2',
        'exchange_weight' => 'decimal:3',
        'exchange_amount' => 'decimal:2',
        'exchange_surplus' => 'decimal:2',
        'customer_deposit_applied' => 'decimal:2',
        'exchange_details' => 'array',
    ];

    public function exchangeMaterial()
    {
        return $this->belongsTo(Material::class, 'exchange_material_id');
    }

    public function exchangeType()
    {
        return $this->belongsTo(Type::class, 'exchange_type_id');
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'order_id', 'id');
    }
    public function returns()
    {
        return $this->hasMany(SalesReturn::class, 'order_id', 'id');
    }
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'order_id', 'id');
    }
   public function labour_items()
    {
        return $this->hasMany(Sales_Labour_Items::class, 'order_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_at)) {
                $model->created_at = Carbon::now('Asia/Kolkata');
            }

            if (empty($model->updated_at)) {
                $model->updated_at = Carbon::now('Asia/Kolkata');
            }
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });
    }
}
