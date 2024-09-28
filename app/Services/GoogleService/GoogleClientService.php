<?php

namespace App\Services\GoogleService;

use Google\Client;
use App\Models\User;
use App\Helpers\GoogleAccessToken;

class GoogleClientService
{
    public function __construct(public User $user)
    {
        //
    }

    public function makeGoogleClient()
    {
        $googleClient = new Client();
        $googleAccessToken = GoogleAccessToken::getGoogleAccessToken($this->user);
        if (!($googleAccessToken)) {
            return false;
        }
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
