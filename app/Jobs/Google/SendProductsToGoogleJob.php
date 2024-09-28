<?php

namespace App\Jobs\Google;

use LogicException;
use App\Models\User;
use App\Models\UserSetting;
use App\Helpers\ErrorLogger;
use Illuminate\Bus\Queueable;
use App\Helpers\GoogleHelpers;
use App\Helpers\GetStoreWebsite;
use App\Helpers\ApplicationSettings;
use App\Helpers\DefaultsArraysHelper;
use App\Helpers\InfoLogger;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Services\GoogleService\GoogleSendProductService;

class SendProductsToGoogleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $storeId;

    public $timeout = 36000;

    public $storeUrl;

    public $googleMerchantId;

    public $lastUpdatedTimeForProduct;

    public $ApplicationSettingsCurrency;

    public $ApplicationSettingsCountry;

    public $mappingSettings;

    public $userSettings;

    public function __construct(public User $user)
    {
        $this->storeId = $user->store_id;
    }

    public function handle()
    {
        $initUserSettings = $this->initUserSettings();
        if (!$initUserSettings) {
            $th = throw new LogicException('UserSetting not found');
            ErrorLogger::logError($th, $this->storeId);
            return false;
        }
        $this->googleMerchantId = $this->userSettings->merchant_id;
        $this->storeUrl = GetStoreWebsite::getStoreWebsiteWithCheck($this->storeId);
        if (!$this->storeUrl) {
            $th = throw new LogicException('Store Url not found');
            ErrorLogger::logError($th, $this->storeId);
            return false;
        }
        $sendProductToGoogleService = $this->makeGoogleProductSendService();
        if ($this->userSettings->rule_query != '' || $this->userSettings->rule_query != null) {
            $this->sendUpdatedAndNewProducts($sendProductToGoogleService);
            $this->sendOldProducts($sendProductToGoogleService);
        }
    }

    private function getAdditionalValues()
    {
        $mappingSettingsSelected = json_decode($this->mappingSettings, true);
        $defaultArray = DefaultsArraysHelper::returnDefaultArray();
        $arrayToReturn = array_keys(array_diff_key($mappingSettingsSelected,  $defaultArray));
        return $arrayToReturn;
    }

    private function initUserSettings()
    {
        $this->ApplicationSettingsCountry = ApplicationSettings::getCountry($this->storeId);
        $this->ApplicationSettingsCurrency = ApplicationSettings::getCurrency($this->storeId);
        $this->userSettings = UserSetting::where('user_store_id', $this->storeId)->first();
        $this->mappingSettings = $this->userSettings->mapping_settings_selected;
        if (!$this->userSettings || !$this->userSettings->merchant_id  || !$this->userSettings->mapping_settings_selected) {
            $th = throw new LogicException('No Merchant Id set to this store Or mapping settings is not set yet');
            ErrorLogger::logError($th,  $this->storeId);
            return false;
        }
        return true;
    }

    private function getStoreUrl()
    {
        $this->storeUrl = GetStoreWebsite::getStoreWebsite($this->storeId);
        if ($this->storeUrl == null) {
            $th = throw new LogicException('Error getting store url');
            ErrorLogger::logError($th,  $this->storeId);
            return false;
        }
        return $this->storeUrl;
    }

    private function sendUpdatedAndNewProducts($sendProductToGoogleService)
    {
        $lastUpdatedNormalProducts = GoogleHelpers::getLastUpdatedData($this->user, false);
        $ruleQueryFromDatabase = str_replace("'",  '"', $this->userSettings->rule_query);
        $ruleQuery = "product.last.update > $lastUpdatedNormalProducts && $ruleQueryFromDatabase";
        $sendProductToGoogleService->sendProductsToGoogle($ruleQuery, false);
    }

    private function sendOldProducts($sendProductToGoogleService)
    {
        $ruleQueryFromDatabase = str_replace("'",  '"', $this->userSettings->rule_query);
        $lastUpdatedOldProducts = GoogleHelpers::getLastUpdatedData($this->user, true);
        if ((time() - $lastUpdatedOldProducts) / (60 * 60 * 24) >= 25) {
            $last25Days = strtotime('-25 day', time());
            $ruleQuery = "product.last.update < $last25Days && $ruleQueryFromDatabase";
            $sendProductToGoogleService->sendProductsToGoogle($ruleQuery, true);
        }
    }

    private function makeGoogleProductSendService()
    {
        return new GoogleSendProductService($this->user, $this->userSettings, $this->storeUrl, $this->googleMerchantId, $this->ApplicationSettingsCountry, $this->ApplicationSettingsCurrency, $this->getAdditionalValues());
    }
}
