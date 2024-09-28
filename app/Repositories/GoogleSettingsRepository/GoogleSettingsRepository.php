<?php

namespace App\Repositories\GoogleSettingsRepository;

use Carbon\Carbon;
use App\Models\GoogleSetting;

class GoogleSettingsRepository
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function save($token)
    {
        $tokenExpiration = Carbon::now()->addSeconds(3599)->timestamp;

        GoogleSetting::where('user_store_id', $this->user->store_id)->updateOrCreate(
            ['google_id' => $this->user->googleSetting->google_id, 'user_store_id' => $this->user->store_id],
            ['google_logged_in' => 1, 'access_token' => $token['access_token'], 'refresh_token' => $token['refresh_token'], 'expiry_date' => $tokenExpiration]
        );
    }
}
