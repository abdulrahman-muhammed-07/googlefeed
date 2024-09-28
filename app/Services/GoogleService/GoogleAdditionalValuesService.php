<?php

namespace App\Services\GoogleService;

use App\Enums\AdditionalKeysEnum;
use App\Helpers\InfoLogger;
use Illuminate\Support\Str;

class GoogleAdditionalValuesService
{
    public function __construct(public $additionalValues, public $googleProduct,  public $googleValuesHelper)
    {
    }

    public  function setKind()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::KIND, $this->additionalValues));
        if ($status) {
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue('kind');
            if (isset($ApplicationValue)) {
                $this->googleProduct->setKind($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdult()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADULT, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADULT;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdult($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setSizes()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::SIZES, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::SIZES;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setSizes($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCustomLabel0()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CUSTOM_LABEL_0, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CUSTOM_LABEL_0;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCustomLabel0($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCustomLabel1()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CUSTOM_LABEL_1, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CUSTOM_LABEL_1;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCustomLabel1($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCustomLabel2()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CUSTOM_LABEL_2, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CUSTOM_LABEL_2;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCustomLabel2($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCustomLabel3()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CUSTOM_LABEL_3, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CUSTOM_LABEL_3;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCustomLabel3($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCustomLabel4()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CUSTOM_LABEL_4, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CUSTOM_LABEL_4;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCustomLabel4($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdsRedirect()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADS_REDIRECT, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADS_REDIRECT;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdsRedirect($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdsGrouping()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADS_GROUPING, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADS_GROUPING;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdsGrouping($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdsLabels()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADS_LABELS, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADS_LABELS;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdsLabels($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAgeGroup()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::AGE_GROUP, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::AGE_GROUP;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAgeGroup($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAvailability()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::AVAILABILITY, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::AVAILABILITY;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAvailability($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAvailabilityDate()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::AVAILABILITY_DATE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::AVAILABILITY_DATE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAvailabilityDate($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdditionalImageLinks()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADDITIONAL_IMAGE_LINK, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADDITIONAL_IMAGE_LINK;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdditionalImageLinks($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setAdditionalSizeType()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ADDITIONAL_SIZE_TYPE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ADDITIONAL_SIZE_TYPE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setAdditionalSizeType($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setCondition()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::CONDITION, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::CONDITION;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setCondition($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setDisplayAdsTitle()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::DISPLAY_ADS_TITLE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::DISPLAY_ADS_TITLE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setDisplayAdsTitle($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setDisplayAdsValue()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::DISPLAY_ADS_VALUE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::DISPLAY_ADS_VALUE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setDisplayAdsValue($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setEnergyEfficiencyClass()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::ENERGY_EFFICIENCY_CLASS, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::ENERGY_EFFICIENCY_CLASS;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setEnergyEfficiencyClass($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setExcludedDestinations()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::EXCLUDED_DESTINATION, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::EXCLUDED_DESTINATION;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setExcludedDestinations($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setExpirationDate()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::EXPIRATION_DATE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::EXPIRATION_DATE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setExpirationDate($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setSizeType()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::SIZE_TYPE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::SIZE_TYPE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setSizeType($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setExternalSellerId()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::EXTERNAL_SELLER_ID, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::EXTERNAL_SELLER_ID;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setExternalSellerId($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setFeedLabel()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::FEED_LABEL, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::FEED_LABEL;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setFeedLabel($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setIdentifierExists()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::IDENTIFIER_EXISTS, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::IDENTIFIER_EXISTS;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setIdentifierExists($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setIsBundle()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::IS_BUNDLE, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::IS_BUNDLE;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setIsBundle($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setMaterial()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::MATERIAL, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::MATERIAL;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setMaterial($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setMultiPack()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::MULTIPACK, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::MULTIPACK;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setMultiPack($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setPickUpMethod()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::PICKUP_METHOD, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::PICKUP_METHOD;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setPickUpMethod($ApplicationValue);
            }
        }
        return $this;
    }

    public  function setLifeStyleImageLinks()
    {
        $status = (AdditionalKeysEnum::isEnabled(AdditionalKeysEnum::LIFESTYLE_IMAGE_LINK, $this->additionalValues));
        if ($status) {
            $value = AdditionalKeysEnum::LIFESTYLE_IMAGE_LINK;
            $ApplicationValue = $this->googleValuesHelper->getApplicationValue($value);
            if (isset($ApplicationValue)) {
                $this->googleProduct->setLifeStyleImageLinks($ApplicationValue);
            }
        }
        return $this;
    }

    final public function setValues()
    {
        return $this->googleProduct;
    }
}
