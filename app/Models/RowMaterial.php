<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RowMaterial extends Model
{
    use HasFactory;

    protected $table = 'row_material';

    protected $casts = [
        'availablility' => 'boolean',
        'isDeleted' => 'boolean',
    ];

    protected $fillable = [
        'vendor_id',
        'category_id',
        'brand_id',
        'branch_id',
        'row_materialname',
        'SKU',
        'hsn_code',
        'barcode',
        'description',
        'price',
        'images',
        'quantity',
        'unit_id',
        'isDeleted',
        'availablility',
        'status',
        'gst_option',
        'product_gst',
        'create_by',
    ];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function row_material_inventory()
    {
        return $this->hasOne(RowMaterialInventory::class, 'row_material_id')
                    ->latestOfMany();
    }

    protected static function booted()
    {
        static::creating(function ($rowMaterial) {
            if (empty($rowMaterial->barcode)) {
                do {
                    $barcode = (string) mt_rand(10000, 99999);
                } while (RowMaterial::where('barcode', $barcode)->exists());

                $rowMaterial->barcode = $barcode;
            }
        });
    }

    public function getImageUrlAttribute()
    {
        $basePath = env('ImagePath', '/');
        $images = $this->images;

        if ($images) {
            $decoded = json_decode($images, true);

            if (is_array($decoded)) {
                return array_map(function ($img) use ($basePath) {
                    return url($basePath . 'storage/' . $img);
                }, $decoded);
            }

            return url($basePath . 'storage/' . $images);
        }

        return [
            url($basePath . 'admin/assets/img/product/noimage.png')
        ];
    }

    public function getAvailablilityAttribute($value)
    {
        return (bool) $value ? 'in_stock' : 'out_stock';
    }

    public function setAvailablilityAttribute($value)
    {
        $this->attributes['availablility'] = in_array(strtolower((string) $value), ['in_stock', '1', 'true', 'yes'], true) ? 1 : 0;
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