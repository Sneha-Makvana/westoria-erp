<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffIncentive extends Model
{
    use HasFactory;
    
    protected $table = 'staff_incentives';
    
    protected $fillable = [
        'user_id',
        'month_year',
        'total_sales',
        'incentive_percentage',
        'incentive_amount',
    ];
}
