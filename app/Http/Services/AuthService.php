<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use App\Helpers\InfoLogger;
use App\Helpers\ErrorLogger;
use GuzzleHttp\Psr7\Request;

class AuthService
{
    protected $client;

    public function __construct()
    {
        //
    }

    public function refreshAccessToken(int $storeId)
    {
        try {
            $user = User::where('store_id', $storeId)->first();
            $responseData = $this->callApplication($user->fresh());
            if (isset($responseData['AccessToken'])) {
                return ['access_token' => $responseData['AccessToken'], 'refresh_token' => $responseData['RefreshToken'], 'expiry' =>  $responseData['Expiry']];
            } else {
                throw new Exception('Failed to refresh access token on service', 500);
            }
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $storeId);
            return null;
        }
    }

    private function callApplication($user)
    {
        $data = $this->makeData($user);
        $client = new Client();
        $curlGetAccessTokenRequest = new Request('POST', 'http://localhost:3333/exchnge', ['Content-Type' => 'application/json'], json_encode($data));
        $resultAccessTokenCurlFetch = $client->sendAsync($curlGetAccessTokenRequest)->wait();
        $resultBody = ($resultAccessTokenCurlFetch->getBody()->getContents());
        return json_decode($resultBody, true);
    }

    private function makeData($user)
    {
        $storeIdRequest = (string) $user->store_id;
        return [
            'store_id' => $storeIdRequest, 'client_id' => env("APP_CLIENT_ID"), 'client_secret' => env("APP_SECRET"), 'redirect_url' => env("REDIRECT_URL"), 'scopes' => env("SCOPE"),
            'ACCESS_TYPE' => env("ACCESS_TYPE"), 'refresh_token' => $user->oauth->refresh_token, 'access_token' => $user->oauth->access_token,  'auth_url' => env("URL_AUTHORIZE"),
            'token_url' => env("URL_ACCESS_TOKEN")
        ];
    }
}
