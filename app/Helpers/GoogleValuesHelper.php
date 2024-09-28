<?php

namespace App\Helpers;

use Google\Service\ShoppingContent\ProductWeight as GoogleProductWeight;
use Google\Service\ShoppingContent\ProductShippingWeight as GoogleProductShippingWeight;
use Illuminate\Support\Str;

class GoogleValuesHelper
{
    public $mappingSettings;

    public $product;

    public $variant;

    public function __construct($mappingSettings, $product, $variant)
    {
        $this->mappingSettings = $mappingSettings;

        $this->product = $product;

        $this->variant = $variant;
    }

    public function cleanUpNamePreg()
    {
        return trim(
            preg_replace('/\s*(?i)((?i)and\s+|with\s+|-\s+)?free shipping\s*/', ' ', $this->product['product_name'])
        );
    }

    public function getProductShippingWeightObject($weightType, $variantWeight)
    {
        $productShippingWeight = new GoogleProductShippingWeight();

        $variantWeight = $variantWeight + $variantWeight * 0.01;

        $productShippingWeight->setValue($variantWeight);

        $productShippingWeight->setUnit($weightType);

        return $productShippingWeight;
    }

    public function getProductWeightObject($weightType, $variantWeight)
    {
        $productWeight = new GoogleProductWeight();

        $productWeight->setValue($variantWeight);

        $productWeight->setUnit($weightType);

        return $productWeight;
    }

    public function getApplicationValue($attribute)
    {
        $value = $this->mappingSettings[$attribute] ?? '';

        $object = '';

        if (str_contains($value, 'product_seo_title')) {
            $result = $this->product['product_seo_title'] ?? '';
            $result = Str::slug($result, '_');
            return $result;
        }

        if (str_contains($value, 'product_categories')) {
            $result = $this->product['product_tags'] ?? '';
            $result = implode('-', $result);
            $result = Str::slug($result, '_');
            return $result;
        }

        if (str_contains($value, 'custom_field_product_')) {
            $object = 'custom_product';
            $value = str_replace('custom_field_product_', '', $value);
        } elseif (str_contains($value, 'custom_field_variant_')) {
            $object = 'custom_variant';
            $value = str_replace('custom_field_variant_', '', $value);
        } elseif (str_contains($value, 'product')) {
            $object = 'product';
        } elseif (str_contains($value, 'variant')) {
            $object = 'variant';
        } elseif (str_contains($value, 'custom_text')) {
            $value = str_replace('custom_text_', '', $value);
            $object = '';
        }

        $result = '';

        if ($object === 'custom_product') {
            $result = $this->product['product_custom_fields'][$value] ?? '';
        } elseif ($object === 'custom_variant') {
            $result = $this->variant['variant_custom_fields'][$value] ?? '';
        } elseif ($object === 'product') {
            $result = $this->product[$value] ?? '';
        } elseif ($object === 'variant') {
            $result = $this->variant[$value] ?? '';
        } else {
            $result = $value;
        }

        return $result;
    }

    public function getSelectedLanguageKey($selectedLanguage)
    {
        $languages = [
            'English' => 'en',
            'French' => 'fr',
            'German' => 'de',
        ];

        if (array_key_exists($selectedLanguage, $languages)) {
            return  $languages[$selectedLanguage];
        }

        return  'en';
    }

    public function getSelectedCountryKey($selectedCountry)
    {
        $countries = [
            'United States' => 'US',
            'Canada' => 'CA',
            'United Kingdom' => 'UK',
        ];

        if (array_key_exists($selectedCountry, $countries)) {
            return $countries[$selectedCountry];
        }

        return 'US';
    }
}
