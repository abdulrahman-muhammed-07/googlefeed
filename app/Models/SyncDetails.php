<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_detail_id',
        'sync_store_id',
        'sync_type',
        'last_updated',
        'last_created',
        'last_sync'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'sync_detail_id';
}
