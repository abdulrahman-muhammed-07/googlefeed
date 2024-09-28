<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_store_id',
        'merchant_id',
        'rule_query',
        'mapping_settings_selected',
        'smtp',
        'service',
        'region',
        'shipping_value',
        'currency'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->rule_query = 'product.status = true';
            $model->region = 'us-east-1';
            $model->service = 'carrier';
            $model->over_sized_products_options =
                json_encode(
                    [
                        'use_settings' => false,
                        'width' => 120,
                        'length' => 120,
                        'height' => 120
                    ]
                );

            $model->over_sized_products_options_default =
                json_encode(
                    [
                        'use_settings' => false,
                        'width' => 120,
                        'length' => 120,
                        'height' => 120
                    ]
                );

            $model->mapping_settings_defaults =
                json_encode(
                    [
                        'id' => 'variant_id',
                        'model' => 'product_model',
                        'name' => 'product_name',
                        'description' => 'product_description',
                        'brand' => 'product_brand',
                        'shipping_label' => 'custom_text_carrier',
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
                        'weight_unit' => 'lbs',
                        'other_settings' => [
                            'shipping_value' => 0,
                            'rule_query' => 'product.status = true',
                            'currency' => "USD"
                        ]
                    ]
                );

            $model->mapping_settings_properties = json_encode(
                [
                    "Application_data" =>
                    [
                        "product_description",
                        "product_name",
                        "variant_id",
                        'product_model',
                        'product_brand',
                        'variant_price',
                        'product_type'
                    ],
                    "google_data" => [
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
                    ],
                    'accepted_values' => [
                        'dimension_unit' => [
                            'in',
                            'cm'
                        ],
                        'weight_unit' => [
                            'kg',
                            'lbs'
                        ]
                    ]
                ]
            );
        });
    }
    protected $primaryKey = 'user_store_id';
}
