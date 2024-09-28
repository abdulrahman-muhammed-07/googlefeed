<?php

namespace App\Models;

use App\Models\Oauth;
use App\Models\UserSetting;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey = 'store_id';

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->store_id;
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->password = Hash::make($user->password);
        });
    }

    public function oauth()
    {
        return $this->hasOne(Oauth::class);
    }

    public function state()
    {
        return $this->hasOne(State::class);
    }

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function googleSetting()
    {
        return $this->hasOne(GoogleSetting::class);
    }
}
