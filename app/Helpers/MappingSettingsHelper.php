<?php

namespace App\Helpers;

class MappingSettingsHelper
{
    public function validateMappingSettings($mappingSettingsSelected)
    {
        $compareArray = DefaultsArraysHelper::returnDefaultArray();

        foreach ($compareArray as $key => $compareValue) {
            if (!array_key_exists($key, $mappingSettingsSelected)) {
                return false;
            }
        }

        return $this->checkMappingValues($mappingSettingsSelected);
    }

    private function checkMappingValues($mappingSettingsSelectedArray)
    {
        $ApplicationProperties =  DefaultsArraysHelper::getApplicationProperties();

        foreach ($mappingSettingsSelectedArray as $key => $oneMappingSetting) {

            $value = true;

            $trimmedValues = array_map('trim', $ApplicationProperties);

            if (!in_array($oneMappingSetting, $trimmedValues)) {
                $value = false;
            } elseif (str_contains($oneMappingSetting, 'custom_field_variant')) {
                $setting = str_replace('custom_field_variant_', $oneMappingSetting, '');
                if (strpos($setting, ' ')) {
                    $value = false;
                }
            } elseif (str_contains($oneMappingSetting, 'custom_field_product')) {
                $setting = str_replace('custom_field_product', $oneMappingSetting, '');
                if (strpos($setting, ' ')) {
                    $value = false;
                }
            }

            return $value;
        }

        return true;
    }
}
