<?php

namespace App\Repositories\SettingsRepository;

use InvalidArgumentException;
use App\Helpers\MerchantHelper;
use App\Helpers\MappingSettingsHelper;
use App\Http\Resources\SettingResource;

class SettingsRepository
{
    const REQUIRED_BOOLEAN = 'required|boolean';
    const REQUIRED_STRING = 'required|string';
    const REQUIRED_INTEGER = 'required|integer';
    const REQUIRED_ARRAY = 'required|array';

    public function __construct(public MerchantHelper $merchantHelper, public MappingSettingsHelper $mappingSettingsCheck)
    {
        //
    }

    public function index($request)
    {
        return  $request->user()->userSetting()->get();
    }

    public function update($request)
    {
        $checkMerchantId = $this->merchantHelper->checkRequestMerchantIdMatches($request);

        $validatedValues = $request->validate([
            "rule_query"   => self::REQUIRED_STRING,
            "currency"   => self::REQUIRED_STRING,
            'merchant_id' => $checkMerchantId ? self::REQUIRED_INTEGER : 'required|integer|in:invalid',
            'mapping_settings_selected' => self::REQUIRED_ARRAY,
            'shipping_value' => self::REQUIRED_INTEGER,
            'over_sized_products_options' =>  self::REQUIRED_ARRAY
        ]);

        $checkMapping = $this->mappingSettingsCheck->validateMappingSettings(
            $validatedValues['mapping_settings_selected']
        );

        if (!$checkMapping) {
            throw new InvalidArgumentException('Error in mapping settings selected.');
        }

        $request->user()->userSetting()->update(
            [
                'merchant_id' => $validatedValues['merchant_id'],
                'mapping_settings_selected' => $validatedValues['mapping_settings_selected'],
                'rule_query' => $validatedValues['rule_query'],
                "service" => $validatedValues['service'] ?? null,
                "service" => $validatedValues['region'] ?? 'us-east-1',
                "currency" => $validatedValues['currency'] ?? "USD",
                "shipping_value" => $validatedValues['shipping_value'] ?? 0,
                "over_sized_products_options" =>  $validatedValues['over_sized_products_options']
            ]
        );

        $request->user()->googleSetting()->update(['saved_init_settings' => 1]);

        $request->user()->fresh();

        return new SettingResource($request->user()->userSetting);
    }

    public function store($request)
    {
        $checkMerchantId = $this->merchantHelper->checkRequestMerchantIdMatches($request);

        $validatedValues =  $request->validate(['merchant_id' => $checkMerchantId ? self::REQUIRED_INTEGER : 'required|integer|in:invalid']);

        $request->user()->userSetting()->firstOrCreate(['merchant_id' => $validatedValues['merchant_id']]);

        if (isset($request->mapping_settings_selected)) {

            if (!$this->mappingSettingsCheck->validateMappingSettings($request->mapping_settings_selected)) {
                throw new InvalidArgumentException('Error in mapping settings selected');
            }

            $validatedValues =  $request->validate([
                'mapping_settings_selected' => self::REQUIRED_ARRAY,
                'currency' => self::REQUIRED_STRING,
                'rule_query' => self::REQUIRED_STRING,
                'shipping_value' => self::REQUIRED_INTEGER,
                'over_sized_products_options' => self::REQUIRED_ARRAY
            ]);

            $request->user()->userSetting()->update([
                'mapping_settings_selected' => $validatedValues['mapping_settings_selected'],
                'currency' => $validatedValues['currency'],
                'shipping_value' => $validatedValues['shipping_value'],
                'rule_query' => $validatedValues['rule_query'],
                'over_sized_products_options' => $validatedValues['over_sized_products_options']
            ]);

            $request->user()->googleSetting()->update(['saved_init_settings' => 1]);

            if (isset($request->region)) {
                $request->user()->userSetting()->update(['region' => $request->region]);
            }

            if (isset($request->service)) {
                $request->user()->userSetting()->update(['service' => $request->service]);
            }
        }

        $request->user()->fresh();

        return $request;
    }
}
