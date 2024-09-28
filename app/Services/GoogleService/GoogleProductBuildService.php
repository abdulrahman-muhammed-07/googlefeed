<?php

namespace App\Services\GoogleService;

use Application\V1\Products\Weight;
use App\Helpers\GoogleValuesHelper;
use App\Helpers\InfoLogger;
use \Google\Service\ShoppingContent\Price;
use Google\Service\ShoppingContent\Product;
use Google\Service\ShoppingContent\ProductShipping;
use \Google\Service\ShoppingContent\Price as GooglePrice;
use Google\Service\ShoppingContent\ProductShippingDimension;
use Google\Service\ShoppingContent\ProductShipping as GoogleProductShipping;

class GoogleProductBuildService
{
    public $timeout = 36000;

    public $googleProduct;

    public $googleMerchantId;

    public $weightType;

    public $googleValuesHelper;

    public $mappingSettings;

    public function __construct(
        public $storeId,
        public $product,
        public $variant,
        public $categoriesSeoUrl,
        public $storeUrl,
        public $userSettings,
        public $additionalValues
    ) {
        $this->googleProduct = new Product();
        $this->weightType =  Weight::name($this->product['product_weight']) ?? 'lbs';
        $this->mappingSettings = json_decode($userSettings->mapping_settings_selected, true);
        $this->googleValuesHelper = new GoogleValuesHelper($this->mappingSettings, $this->product, $this->variant);
    }

    public function setGoogleProductWeight()
    {
        $productWeightObject = $this->googleValuesHelper->getProductWeightObject(
            $this->weightType,
            $this->variant['variant_weight']
        );
        $this->googleProduct->setProductWeight($productWeightObject);
        return $this;
    }

    public function setGoogleProductShippingWeight()
    {
        $productShippingWeightObject =  $this->googleValuesHelper->getProductShippingWeightObject(
            $this->weightType,
            $this->variant['variant_weight']
        );
        $this->googleProduct->setShippingWeight($productShippingWeightObject);
        return $this;
    }

    public function setGoogleProductSellOnGoogleQuantity()
    {
        $this->googleProduct->setSellOnGoogleQuantity((string)$this->variant['variant_qty']);
        return $this;
    }

    public function setGoogleProductModel()
    {
        $value = $this->googleValuesHelper->getApplicationValue('product_model');
        if ($value) {
            $this->googleProduct->setMpn($value);
            $this->googleProduct->setIdentifierExists(true);
        }
        return $this;
    }

    public function setGoogleProductBrand()
    {
        $value = $this->googleValuesHelper->getApplicationValue('product_brand');
        if ($value) {
            $this->googleProduct->setBrand(($value));
        }
        return $this;
    }

    public function setGoogleProductDescription()
    {
        $value = $this->googleValuesHelper->getApplicationValue('description');
        if ($value) {
            $this->googleProduct->setDescription(($value));
        }
        return $this;
    }

    public function setGoogleProductTitle()
    {
        $value = $this->googleValuesHelper->getApplicationValue('name');
        if ($value) {
            $this->googleProduct->setTitle($this->googleValuesHelper->cleanUpNamePreg($value));
        }
        return $this;
    }

    public function setGoogleProductId()
    {
        $value = $this->googleValuesHelper->getApplicationValue('id');
        if ($value) {
            $this->googleProduct->setId($value);
        }
        return $this;
    }

    public function setGoogleProductPrice()
    {
        $value = $this->googleValuesHelper->getApplicationValue('price');
        if ($value) {
            $price = new Price();
            $price->setValue($value);
            $price->setCurrency(isset($this->variant['variant_currency_code'])
                ? $this->variant['variant_currency_code'] : '');
            $this->googleProduct->setPrice($price);
        }
        return $this;
    }

    public function setGoogleProductSalePrice()
    {
        $value = $this->googleValuesHelper->getApplicationValue('sale_price');
        if ($value && $this->variant['variant_on_sale'] == true) {
            $salePrice = new Price();
            $salePrice->setValue($value);
            $salePrice->setCurrency(isset($this->variant['variant_currency_code'])
                ? $this->variant['variant_currency_code'] : '');
            $this->googleProduct->setSalePrice($salePrice);
        }
        return $this;
    }

    public function setGoogleProductStockStatus()
    {
        if ($this->product['product_status'] == true && $this->variant['variant_status'] == true) {
            $this->googleProduct->setAvailability('in stock');
        } else {
            $this->googleProduct->setAvailability('out of stock');
        }
        return $this;
    }

    public function setGoogleProductImages()
    {
        $productImagesArray = [];
        foreach ($this->product['product_images'] as $productImage) {
            $productImagesArray[] = $productImage;
        }
        if ($productImagesArray != []) {
            $this->googleProduct->setAdditionalImageLinks($productImagesArray);
        }
        foreach ($this->product['product_images'] as $productImage) {
            $this->googleProduct->setImageLink(empty($this->variant['variant_image'])
                ?  $productImage : $this->variant['variant_image']);
        }
        return $this;
    }

    public function setGoogleProductColor()
    {
        $value = $this->googleValuesHelper->getApplicationValue('color');
        if ($value) {
            $this->googleProduct->setColor($value);
        }
        return $this;
    }

    public function setGoogleProductItemGroupId()
    {
        $value = $this->googleValuesHelper->getApplicationValue('item_group_id');
        if ($value) {
            $this->googleProduct->setItemGroupId(md5($value));
        }
        return $this;
    }

    public function setGoogleProductGtin()
    {
        $value = $this->googleValuesHelper->getApplicationValue('gtin');
        if ($value) {
            $this->googleProduct->setGtin($value);
        }
        return $this;
    }

    public function setGoogleProductShippingLabel()
    {
        $customFields = $this->variant['variant_custom_fields'];

        $overSizedSettings = json_decode($this->userSettings->over_sized_products_options, true);

        if (
            isset($customFields) &&
            isset($customFields['free_shipping']) &&
            $customFields['free_shipping']
        ) {
            $cheapestShippingPrice = new GooglePrice;
            $cheapestShippingPrice->setValue(0);
            $cheapestShippingPrice->setCurrency("USD");
            $productShipping = new GoogleProductShipping();
            $productShipping->setPrice($cheapestShippingPrice);
            $productShipping->setCountry("US");
            $productShipping->setService("UPS");
            $this->googleProduct->setShipping($productShipping);
            $this->googleProduct->setShippingLabel('Free Shipping');
        } elseif ($overSizedSettings['use_settings']) {
            if (
                $this->variant['variant_width'] > $overSizedSettings['width']  ||
                $this->variant['variant_length'] > $overSizedSettings['length']  ||
                $this->variant['variant_height'] > $overSizedSettings['height']
            ) {
                $this->googleProduct->setShippingLabel('Oversized');
            }
        } else {
            $this->googleProduct->setShippingLabel('carrier');
        }
        return $this;
    }

    public function setGoogleProductCondition()
    {
        $value = $this->googleValuesHelper->getApplicationValue('condition');
        if ($value) {
            $this->googleProduct->setCondition($value);
        }
        return $this;
    }

    public function setGoogleProductCategory()
    {
        $value = $this->googleValuesHelper->getApplicationValue('google_product_category');
        if ($value) {
            $this->googleProduct->setGoogleProductCategory($value);
        }
        return $this;
    }

    public function setGoogleProductGender()
    {
        $value = $this->googleValuesHelper->getApplicationValue('gender');
        if ($value) {
            $this->googleProduct->setGender($value);
        }
        return $this;
    }

    public function setGoogleProductShippingDimensions()
    {
        if (isset($this->variant['variant_height'])) {
            $productShippingDimension = new ProductShippingDimension();
            $productShippingDimension->setValue($this->variant['variant_height']);
            $productShippingDimension->setUnit($this->mappingSettings['dimension_unit']);
            $this->googleProduct->setShippingHeight($productShippingDimension);
        }
        if (isset($this->variant['variant_length'])) {
            $productShippingDimension = new ProductShippingDimension();
            $productShippingDimension->setValue($this->variant['variant_length']);
            $productShippingDimension->setUnit($this->mappingSettings['dimension_unit']);
            $this->googleProduct->setShippingLength($productShippingDimension);
        }
        if (isset($this->variant['variant_width'])) {
            $productShippingDimension = new ProductShippingDimension();
            $productShippingDimension->setValue($this->variant['variant_width']);
            $productShippingDimension->setUnit($this->mappingSettings['dimension_unit']);
            $this->googleProduct->setShippingWidth($productShippingDimension);
        }
        return $this;
    }

    public function setGoogleProductBarCode()
    {
        $value = $this->googleValuesHelper->getApplicationValue('barcode');
        if ($value) {
            $this->googleProduct->setGtin($value);
        }
        return $this;
    }

    public function setGoogleProductOfferId()
    {
        $offerId = md5($this->product['product_id'] . $this->variant['variant_id']);
        $this->googleProduct->setOfferId($offerId);
        return $this;
    }

    public function setGoogleProductContentLanguage()
    {
        $value = $this->googleValuesHelper->getApplicationValue('content_language');
        if ($value) {
            $selectedLanguage = $this->googleValuesHelper->getSelectedLanguageKey($value);
            $this->googleProduct->setContentLanguage($selectedLanguage);
        }
        return $this;
    }

    public function setGoogleProductTargetCountry()
    {
        $value = $this->googleValuesHelper->getApplicationValue('target_country');
        if ($value) {
            $selectedCountry = $this->googleValuesHelper->getSelectedCountryKey($value);
            $this->googleProduct->setTargetCountry($selectedCountry);
        }
        return $this;
    }

    public function setGoogleProductShipping($userSettings, $ApplicationSettingsCountry, $ApplicationSettingsCurrency)
    {
        if ($userSettings->shipping_value > 0 && isset($userSettings->service) && isset($userSettings->region)) {
            $cheapestShippingPrice = new Price;
            $cheapestShippingPrice->setValue($userSettings->shipping_value);
            $cheapestShippingPrice->setCurrency($ApplicationSettingsCurrency);
            $productShipping = new ProductShipping();
            $productShipping->setCountry($ApplicationSettingsCountry);
            $productShipping->setService($userSettings->service);
            $productShipping->setRegion($userSettings->region);
            $productShipping->setPrice($cheapestShippingPrice);
            $this->googleProduct->setShipping($productShipping);
        }
        return $this;
    }

    public function setGoogleProductLink()
    {
        if (isset($this->categoriesSeoUrl[0])) {
            $productLink = 'https://' . $this->storeUrl . '/categories/' . $this->categoriesSeoUrl[0]
                . '/product/' . $this->product['product_seo_url'] . '?utm_source=google&utm_medium=cpc';
            $this->googleProduct->setLink($productLink);
        }
        return $this;
    }

    public function setGoogleProductChannel($string = 'online')
    {
        $this->googleProduct->setChannel($string);
        return $this;
    }

    public function setGoogleAdditionalValues($additionalValues)
    {
        $googleAdditionalValuesService =
            new GoogleAdditionalValuesService(
                $additionalValues,
                $this->googleProduct,
                $this->googleValuesHelper
            );

        $googleAdditionalValuesService->setKind()->setSizes()->setAdsLabels()->setAdsGrouping()->setAvailability()->setAvailabilityDate()->setExpirationDate()->setAgeGroup()->setDisplayAdsValue()->setCustomLabel0()->setCustomLabel1()->setIsBundle()->setPickUpMethod()->setValues();
        return $this;
    }

    public function createFinalGoogleProductObject()
    {
        return  $this->googleProduct;
    }
}
