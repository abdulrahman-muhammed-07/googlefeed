<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_store_id',
        'google_id',
        'google_logged_in',
        'saved_init_settings',
        'sync_status',
        'access_token',
        'refresh_token',
        'expiry_date'
    ];

    public $primaryKey = 'google_id';
}
