<?php

namespace App\Helpers;

use App\Models\UserSetting;
use Application\V1\Money\Currency;

class ApplicationSettings
{
    public static function getSettings($storeId)
    {
        $ApplicationSettingsClient = ClientsBuilder::getSettingsClient();

        $ApplicationSettingsRequest = ClientsBuilder::getSettingsRequest();

        $settings = $ApplicationSettingsClient->GetStoreSettings($ApplicationSettingsRequest, MetaData::get($storeId));

        $settings = $settings->wait();

        if ($settings[0] == null || !isset($settings[0])) {
            return null;
        }

        $settings = $settings[0]->getGeneralSettings();

        return $settings;
    }

    public static function getCountry($storeId)
    {
        $settings = self::getSettings($storeId);

        if (!$settings || $settings == null) {
            return "US";
        }

        $storeDetails = $settings->getStoreAddress();

        $ApplicationSettingsCountryCode = $storeDetails->getCountryCode() != null
            ? $storeDetails->getCountryCode()->getValue() : null;

        $userSetSettingsCountry = UserSetting::where('user_store_id', $storeId)->first();

        if (isset($userSetSettingsCountry->useCountry) && $userSetSettingsCountry->useCountry) {

            if (!$userSetSettingsCountry ||   $userSetSettingsCountry != null ||   $userSetSettingsCountry != []) {

                $ApplicationSettingsCountryCode =   $userSetSettingsCountry->country;
            } else {
                $ApplicationSettingsCountryCode = "US";
            }
        }

        if ($ApplicationSettingsCountryCode == null) {

            $ApplicationSettingsCountryCode = "US";
        }

        return $ApplicationSettingsCountryCode;
    }

    public static function getCurrency($storeId)
    {
        $settings = self::getSettings($storeId);

        if (!$settings || $settings == null) {
            return "USD";
        }

        $storeDetails = $settings->getStoreCurrency();

        $ApplicationSettingsCurrencyCodeGet = $storeDetails->getCurrency();

        $ApplicationSettingsCurrencyCode = Currency::name($ApplicationSettingsCurrencyCodeGet);

        $userSetSettingsCurrency = UserSetting::where('user_store_id', $storeId)->first();

        if (isset($userSetSettingsCurrency->useCurrency) && $userSetSettingsCurrency->useCurrency == true) {

            if (!$userSetSettingsCurrency ||   $userSetSettingsCurrency != null ||   $userSetSettingsCurrency != []) {

                $ApplicationSettingsCurrencyCode =   $userSetSettingsCurrency->currency;
            } else {
                $ApplicationSettingsCurrencyCode = "USD";
            }
        }

        if ($ApplicationSettingsCurrencyCode == null) {

            $ApplicationSettingsCurrencyCode = "USD";
        }

        return $ApplicationSettingsCurrencyCode;
    }
}
