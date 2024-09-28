<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class ReportAccessTokenExpiry
{
    static function report($user)
    {
        if (env("REPORT_TOKENS_EXPIRY") == true) {

            $link = env('PLUGIN_LINK');

            $appName = env('APP_NAME');
        }
    }
}
