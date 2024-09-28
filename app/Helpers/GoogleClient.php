<?php

namespace App\Helpers;

use App\Models\User;
use Google\Client;

class  GoogleClient
{
    public static function makeClient(User $user)
    {
        $googleClient = new Client();
        $googleAccessToken = GoogleAccessToken::getGoogleAccessToken($user);
        $googleClient->setAccessToken($googleAccessToken);
        $googleClient->addScope(
            'https://www.googleapis.com/auth/content',
            'https://www.googleapis.com/auth/structuredcontent',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/plus.business.manage'
        );

        return $googleClient;
    }
}
