<?php

namespace App\Helpers;

use App\Models\UserSetting;
use Exception;

class DatabaseSettings
{
    static function getDatabaseUserSettings($storeId)
    {
        $databaseSettings = UserSetting::where('user_store_id', $storeId)->first();

        return $databaseSettings;
    }

    static function getShippingRegion($storeId)
    {
        $settings = self::getDatabaseUserSettings($storeId);

        $region =  isset($settings->region) ? $settings->region : null;

        if ($region == null) {

            self::LogErrorForSettings($storeId, 'Shipping region');
        }

        return $region;
    }

    static function getShippingPrice($storeId)
    {
        $settings = self::getDatabaseUserSettings($storeId);

        $value = isset($settings->shipping_value) ? $settings->shipping_value : null;

        if ($value == null) {

            self::LogErrorForSettings($storeId, 'Shipping price value');
        }

        return $value;
    }

    static function getShippingService($storeId)
    {
        $settings = self::getDatabaseUserSettings($storeId);

        $service = isset($settings->service) ? $settings->service : null;

        if ($service == null) {

            self::LogErrorForSettings($storeId, 'Service');
        }

        return $service;
    }

    static function getUseShippingRulesStatus($storeId)
    {
        $settings = self::getDatabaseUserSettings($storeId);

        $useShippingSettings = isset($settings->use_shipping_settings) ? $settings->use_shipping_settings : false;

        return $useShippingSettings;
    }

    static function LogErrorForSettings($storeId, $type)
    {
        $th = new Exception("$type in settings is not set and that might affect product on Google ", 404);

        ErrorLogger::logError($th, $storeId);
    }
}
