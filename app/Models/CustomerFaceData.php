<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFaceData extends Model
{
    use HasFactory;

    protected $table = 'customer_face_data';

    protected $fillable = [
        'customer_id',
        'face_embedding',
        'photo_url',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
