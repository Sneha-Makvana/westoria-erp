<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'product_variant_id',
        'product_gst_details',
        'product_gst_total',
        'category_id',
        'delivered_quantity',
        'price',
        'discount_percentage',
        'discount_amount',
        'quantity',
        'total_amount',
        'imei_no',
        'branch_id',
        'created_by',
        'isDeleted',
        'rental_quantity',
        'is_rental',
        'rent_value',
        'rent_price',
        'rent_period',
        'rent_start_at',
        'rent_end_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'product_gst_details' => 'array',
        'quantity' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Order::class, 'order_id'); // Assuming invoice means the order
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function checkRentalAvailability(
        int $productId,
        string $rentStart,
        string $rentEnd,
        int $requestedQty
    ): bool {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        $rentStart = Carbon::parse($rentStart);
        $rentEnd = Carbon::parse($rentEnd);
        $now = Carbon::now();

        // Get TOTAL quantity from database (this is current physical stock)
        // $totalProductQty = $product->initial_qty ?? $product->quantity;
        $totalProductQty = $product->quantity;

        // Calculate how many are currently rented out and overlapping with our requested period
        $overlappingRentalsQty = self::where('product_id', $productId)
            ->where('is_rental', 1)
            ->where(function ($q) use ($rentStart, $rentEnd) {
                // Check for any overlap between existing rentals and requested period
                $q->where(function ($q2) use ($rentStart, $rentEnd) {
                    // Existing rental starts during requested period
                    $q2->where('rent_start_at', '>=', $rentStart)
                       ->where('rent_start_at', '<', $rentEnd);
                })->orWhere(function ($q2) use ($rentStart, $rentEnd) {
                    // Existing rental ends during requested period
                    $q2->where('rent_end_at', '>', $rentStart)
                       ->where('rent_end_at', '<=', $rentEnd);
                })->orWhere(function ($q2) use ($rentStart, $rentEnd) {
                    // Existing rental completely contains requested period
                    $q2->where('rent_start_at', '<=', $rentStart)
                       ->where('rent_end_at', '>=', $rentEnd);
                })->orWhere(function ($q2) use ($rentStart, $rentEnd) {
                    // Requested period completely contains existing rental
                    $q2->where('rent_start_at', '>=', $rentStart)
                       ->where('rent_end_at', '<=', $rentEnd);
                });
            })
            // Only count rentals that are still active during our period
            ->where('rent_end_at', '>', $rentStart)
            ->sum(\Illuminate\Support\Facades\DB::raw('quantity - rental_quantity'));

        // Available quantity = Total stock - overlapping rentals
        $availableForPeriod = $totalProductQty - $overlappingRentalsQty;

        return $availableForPeriod >= $requestedQty;
    }

    public static function getActiveRentedQtyOnDate(
        int $productId,
        string $dateTime
    ): int {
        $dateTime = Carbon::parse($dateTime);

        return self::where('product_id', $productId)
            ->where('is_rental', 1)
            ->where('rent_start_at', '<=', $dateTime)
            ->where('rent_end_at', '>', $dateTime)
            ->sum(\Illuminate\Support\Facades\DB::raw('quantity - rental_quantity'));
    }
}
