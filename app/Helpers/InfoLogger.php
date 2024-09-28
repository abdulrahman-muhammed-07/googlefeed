<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class InfoLogger
{
    static function logInfo($message, $storeId, $additionalMessage = null): void
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        if (is_array($additionalMessage)) {
            $additionalMessage = json_encode($additionalMessage);
        }
        $appName = env("APP_NAME");
        $message = !$additionalMessage ? $message . " in store " . $storeId : $message . " in store " . $storeId . 'and ' . $additionalMessage;
        Config::set("logging.channels.info-$storeId", ['driver' => 'daily', 'path' => storage_path("logs/$appName-$storeId.log"), 'level' => 'debug', 'days' => 7]);
        Log::channel("info-$storeId")->info($message, self::getContext());
    }

    static function getContext()
    {
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        $trace = array_reverse($trace);
        array_shift($trace);
        array_pop($trace);
        $trace = array_reverse($trace);
        $context = ['file' => $e->getFile(), 'line' => $e->getLine(),   'trace' => $trace];
        return $context;
    }
}
