<?php

namespace App\Helpers;

use Google\Client;
use App\Models\User;
use App\Models\GoogleSetting;
use App\Repositories\GoogleSettingsRepository\GoogleSettingsRepository;

class GoogleAccessToken
{
    public static function getGoogleAccessToken(User $user)
    {
        $googleAccessToken = $user->googleSetting->access_token;
        if (time() > (int) $user->googleSetting->expiry_date) {
            $googleAccessToken = self::googleNewAccessToken($user);
        }
        if (!$googleAccessToken) {
            ReportAccessTokenExpiry::report($user);
            return null;
        }
        return $googleAccessToken;
    }

    public static function googleNewAccessToken(User $user): ?string
    {
        $refreshToken = GoogleSetting::where('user_store_id', $user->store_id)->first()->refresh_token ?: '';
        if ($refreshToken == '') {
            return null;
        }
        $client = new Client();
        $client->setClientId(env("GOOGLE_CLIENT_ID"));
        $client->setClientSecret(env("GOOGLE_CLIENT_SECRET"));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URL'));
        $client->setAccessType(env('GOOGLE_ACCESS_TYPE'));
        $client->setApprovalPrompt(env('GOOGLE_APPROVAL_PROMPT'));
        $client->refreshToken($refreshToken);
        $token = $client->getAccessToken();
        if (!$token || !isset($token['errors'])) {
            $saveGoogleSettings = new GoogleSettingsRepository($user);
            $saveGoogleSettings->save($token);
            return $token['access_token'];
        }
        return null;
    }
}
