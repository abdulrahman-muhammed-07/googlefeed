<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ErrorLogger
{
    static function logError($th, $storeId, $additionalMessage = null): void
    {
        $appName = env("APP_NAME");
        Config::set("logging.channels.custom-$storeId", ['driver' => 'daily', 'path' => storage_path("logs/$appName-$storeId.log"), 'level' => 'debug', 'days' => 14]);
        $message = !$additionalMessage ? ($th->getMessage() . " in store " . $storeId) : ($th->getMessage() . " in store " . $storeId . 'and ' . $additionalMessage);
        $context = ['stacktrace' => $th->getTraceAsString()];
        Log::channel("custom-$storeId")->error($message, $context);
    }
}
