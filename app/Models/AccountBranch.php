<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBranch extends Model
{
    use HasFactory;

    protected $table = 'account_branches';

    protected $fillable = [
        'branch_id',
        'name',
        'branch_code',
        'branch_company_name',
        'email',
        'phone',
        'phone_2',
        'address',
        'address_line_2',
        'address_line_3',
        'city',
        'area',
        'state',
        'zip_code',
        'tin',
        'gstin',
        'pan',
        'opened_on',
        'closed_on',
        'branch_type',
        'status',
        'isDeleted',
        'create_by',
    ];
}
