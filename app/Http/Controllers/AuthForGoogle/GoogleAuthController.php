<?php

namespace App\Http\Controllers\AuthForGoogle;

use Illuminate\Http\Request;
use App\Models\GoogleSetting;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        if (!$request->user() || !$request->user()->store_id) {
            return response()->json(['status' => 'error',   'message' => 'please provide store Id to continue', 'code' =>  401]);
        }
        $scopes = [env("GOOGLE_SCOPE_FOR_USER_EMAIL"), env("GOOGLE_SCOPE_FOR_USER_PROFILE"), env("GOOGLE_SCOPE_FOR_AUTH_CONTENT"), env("GOOGLE_SCOPE_FOR_AUTH_STRUCTURED_CONTENT"), env("GOOGLE_SCOPE_FOR_AUTH_PLUS_BUSINESS_MANAGE")];
        $parameters = ['access_type' => env("GOOGLE_ACCESS_TYPE"),  "prompt" => env('GOOGLE_APPROVAL_PROMPT'),  'state' => $request->user()->store_id];
        session()->put('store_id', $request->user()->store_id);
        $driver = Socialite::driver('google')->scopes($scopes)->with($parameters);
        $redirect = $driver->redirect()->getTargetUrl();
        return response()->json(["url" => $redirect]);
    }

    public function handleGoogleCallback(Request $request)
    {
        $storeId = $request->get('state');
        $user = Socialite::driver('google')->stateless()->user();
        if (!$storeId || !$user || !$user->id) {
            return response()->json(['status' => 'error', 'message' => 'Authentication process failed or took too long to respond. Please try again.']);
        }
        try {
            GoogleSetting::where('user_store_id', $storeId)->updateOrCreate(
                ['google_id' => $user->id, 'user_store_id' => $storeId],
                ['google_logged_in' => true,  'access_token' => $user->token, 'refresh_token' => $user->refreshToken, 'expiry_date' => time() + 3599]
            );
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
        echo "Done Logging In To Google. Please Close this Window and go back to GoogleFeed App.";
    }
}
