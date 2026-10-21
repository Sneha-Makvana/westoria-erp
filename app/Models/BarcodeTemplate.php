<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeTemplate extends Model
{
    use HasFactory;

    protected $table = 'barcode_templates';

    protected $fillable = [
        'name',
        'width_mm',
        'height_mm',
        'orientation',
        'printer_type',
        'dpi',
        'margin_top',
        'margin_bottom',
        'margin_left',
        'margin_right',
        'horizontal_gap',
        'vertical_gap',
        'design_json',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        // Optional: cast design_json to array if you want array manipulation natively
        // 'design_json' => 'array', 
    ];
}
