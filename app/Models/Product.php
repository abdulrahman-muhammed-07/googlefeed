<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_store_id',
        'product_id',
        'variant_id',
        'response',
        'product_name',
        'product_image',
        'variant_option',
        'batch_id',
        'offer_id',
        'is_excluded',
        'google_error_array',
        'status'
    ];

    protected $primaryKey = 'variant_id';

    public $incrementing = false;

    public $casts = [
        'updated_at' => 'timestamp',
        'created_at' => 'timestamp',
        'google_error_array' => 'array'
    ];

    public function getErrorLogAttribute($details)
    {
        return json_decode($details, true);
    }

    public function getGoogleErrorArrayAttribute($details)
    {
        return json_decode($details, true);
    }
}
