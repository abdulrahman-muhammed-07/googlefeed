<?php

namespace App\Helpers;

use Google\Protobuf\GPBEmpty;

class GetStoreWebsite
{
    public static function getStoreWebsite(int $storeId)
    {
        $settingsClient = ClientsBuilder::getSettingsClient();
        $settingRequest = new GPBEmpty();
        $settings = $settingsClient->GetStoreWebsite($settingRequest, MetaData::get($storeId));
        $settings = $settings->wait();
        if (!GrpcErrorHandle::checkGrpcErrors($settings, $storeId)['status']) {
            return false;
        }
        return $settings[0]->getGeneralSettings()->getStoreDetails()->getStoreWebsite();
    }

    public static function getStoreName($storeId)
    {
        $settingsClient = ClientsBuilder::getSettingsClient();
        $settingRequest = new GPBEmpty();
        $settings = $settingsClient->GetStoreSettings($settingRequest, MetaData::get($storeId));
        $settings = $settings->wait();
        if (!GrpcErrorHandle::checkGrpcErrors($settings, $storeId)['status']) {
            return false;
        }
        return $settings[0]->getGeneralSettings()->getStoreDetails()->getStoreNameUnwrapped();
    }

    public static function getStoreWebsiteWithCheck(int $storeId)
    {
        $website = self::getStoreWebsite($storeId);
        if ($website == null || empty($website) || $website == false) {
            if ($storeId == 25 || $storeId == '25') {
                $website = 'bigpawoliveoil.com';
            }
            if ($storeId == 28 || $storeId == '28') {
                $website = 'radiogagas.com';
            }
            if ($storeId == 31 || $storeId == '31') {
                $website = 'bestprolighting.com';
            }
        }
        return $website;
    }

}
