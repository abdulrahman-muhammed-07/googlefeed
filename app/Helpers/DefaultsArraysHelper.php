<?php

namespace App\Helpers;

class DefaultsArraysHelper
{
    public static function returnDefaultArray()
    {
        return [
            'id' => 'variant_id',
            'model' => 'product_model',
            'name' => 'product_name',
            'description' => 'product_description',
            'brand' => 'product_brand',
            'shipping_label' => 'carrier',
            'price' => 'variant_price',
            'sale_price' => 'variant_discount_price',
            'condition' => 'custom_field_variant_condition',
            'gtin' => 'custom_field_variant_gtin',
            'google_product_category' => 'custom_field_product_google_product_category',
            "gender" => "custom_field_variant_gender",
            'content_language' => 'English',
            'target_country' => "United States",
            "color" => "custom_field_variant_color",
            'item_group_id' => "product_id",
            "barcode" => "variant_barcode",
            'dimension_unit' => 'in',
            'weight_unit' => 'lbs'
        ];
    }

    public static function getApplicationProperties()
    {
        return [
            "product_description",
            "product_name",
            "variant_id",
            'variant_barcode',
            'product_model',
            'product_brand',
            'variant_price',
            'product_type'
        ];
    }

    public static function getGoogleProperties()
    {
        return [
            'custom_label_0',
            'custom_label_1',
            'custom_label_2',
            'custom_label_3',
            'custom_label_4',
            'ads_redirect',
            'ads_grouping',
            'ads_labels',
            'adult',
            'age_group',
            'availability',
            'availability_date',
            'additional_image_link',
            'additional_size_type',
            'condition',
            'display_ads_title',
            'display_ads_value',
            'energy_efficiency_class',
            'excluded_destination',
            'expiration_date',
            'external_seller_id',
            'feed_label',
            'gender',
            'identifier_exists',
            'is_bundle',
            'kind',
            'lifestyle_image_link',
            'material',
            'multipack',
            'pickup_method',
            'size_type',
            'sizes',
            'mpn'
        ];
    }
}
