<?php

namespace App\Services\GoogleService;

use App\Models\User;
use App\Helpers\GoogleHelpers;
use App\Helpers\InfoLogger;
use Google\Service\ShoppingContent\ProductsCustomBatchRequestEntry;

class GoogleMakeEntryProductToSend
{
    public function __construct(
        public User $user,
        public $product,
        public $variant,
        public $storeUrl,
        public $userSettings,
        public $additionalValues,
        public $offerId = null
    ) {
        //
    }

    public function makeNewEntryForProduct(
        $productId,
        $variantId,
        $googleMerchantId,
        $ApplicationSettingsCountry,
        $ApplicationSettingsCurrency,
        $method = 'insert'
    ) {
        $googleProductEntry = new ProductsCustomBatchRequestEntry();
        $googleProductEntry->setMethod($method);
        $googleProductEntry->setBatchId(crc32($productId . $variantId));
        if (isset($this->offerId)) {
            $googleProductEntry->setProductId($this->offerId);
        }
        $googleProduct = $this->makeGoogleProductObject($ApplicationSettingsCountry, $ApplicationSettingsCurrency);
        if (isset($googleProduct) && !empty($googleProduct)) {
            $googleProductEntry->setProduct($googleProduct);
        }
        $googleProductEntry->setMerchantId($googleMerchantId);
        return $googleProductEntry;
    }

    private function makeGoogleProductObject($ApplicationSettingsCountry, $ApplicationSettingsCurrency)
    {
        $userSettings = $this->user->userSetting;
        $googleProductService = new GoogleProductBuildService(
            $this->user->store_id,
            $this->product,
            $this->variant,
            GoogleHelpers::getProductCategorySeoUrl($this->product, $this->user),
            $this->storeUrl,
            $this->userSettings,
            $this->additionalValues
        );


        return  $googleProductService->setGoogleProductLink()
            ->setGoogleProductId()
            ->setGoogleProductTitle()
            ->setGoogleProductDescription()
            ->setGoogleProductShippingLabel()
            ->setGoogleProductBrand()
            ->setGoogleProductModel()
            ->setGoogleProductSellOnGoogleQuantity()
            ->setGoogleProductWeight()
            ->setGoogleProductShippingWeight()
            ->setGoogleProductContentLanguage()
            ->setGoogleProductBarCode()
            ->setGoogleProductChannel()
            ->setGoogleProductGender()
            ->setGoogleProductImages()
            ->setGoogleProductStockStatus()
            ->setGoogleAdditionalValues($this->additionalValues)
            ->setGoogleProductShippingDimensions()
            ->setGoogleProductColor()
            ->setGoogleProductPrice()
            ->setGoogleProductSalePrice()
            ->setGoogleProductItemGroupId()
            ->setGoogleProductOfferId()
            ->setGoogleProductGtin()
            ->setGoogleProductTargetCountry()
            ->setGoogleProductShipping($userSettings, $ApplicationSettingsCountry, $ApplicationSettingsCurrency)
            ->createFinalGoogleProductObject();
    }
}

