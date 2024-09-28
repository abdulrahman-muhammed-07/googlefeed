<?php

namespace App\Enums;

use App\Helpers\InfoLogger;

enum AdditionalKeysEnum: int
{
    const KIND = 'kind';
    const ADULT = 'adult';
    const SIZES = 'sizes';
    const MATERIAL = 'material';
    const AGE_GROUP = 'age_group';
    const IS_BUNDLE = 'is_bundle';
    const MULTIPACK = 'multipack';
    const SIZE_TYPE = 'size_type';
    const ADS_LABELS = 'ads_labels';
    const FEED_LABEL = 'feed_label';
    const ADS_GROUPING = 'ads_grouping';
    const ADS_REDIRECT = 'ads_redirect';
    const AVAILABILITY = 'availability';
    const PICKUP_METHOD = 'pickup_method';
    const CUSTOM_LABEL_0 = 'custom_label_0';
    const CUSTOM_LABEL_1 = 'custom_label_1';
    const CUSTOM_LABEL_2 = 'custom_label_2';
    const CUSTOM_LABEL_3 = 'custom_label_3';
    const CUSTOM_LABEL_4 = 'custom_label_4';
    const EXPIRATION_DATE = 'expiration_date';
    const AVAILABILITY_DATE = 'availability_date';
    const DISPLAY_ADS_TITLE = 'display_ads_title';
    const DISPLAY_ADS_VALUE = 'display_ads_value';
    const IDENTIFIER_EXISTS = 'identifier_exists';
    const EXTERNAL_SELLER_ID = 'external_seller_id';
    const ADDITIONAL_SIZE_TYPE = 'additional_size_type';
    const EXCLUDED_DESTINATION = 'excluded_destination';
    const LIFESTYLE_IMAGE_LINK = 'lifestyle_image_link';
    const ADDITIONAL_IMAGE_LINK = 'additional_image_link';
    const ENERGY_EFFICIENCY_CLASS = 'energy_efficiency_class';
    const CONDITION = 'condition';

    public static function isEnabled($key,  $additionalValues)
    {
        $arrayKeyStatus = in_array($key,  $additionalValues);
        if ($arrayKeyStatus) {
            return true;
        }
        return false;
    }
}
