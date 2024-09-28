<?php

namespace App\Google;


use Google\Service\ShoppingContent;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/** @deprecated */
class GetGoogleMerchantId
{

    // public function getGoogleMerchantId($googleClient)
    // {

    //     $googleService = new ShoppingContent($googleClient);

    //     $response = $googleService->accounts->authinfo();

    //     if (!isset($response) || is_null($response->getAccountIdentifiers())) {
    //         throw new InvalidArgumentException(
    //             'Authenticated user has no access to any Merchant Center accounts'
    //         );
    //         return Log::error("Authenticated user has no access to any Merchant Center accounts");
    //     }

    //     $firstAccount = $response->getAccountIdentifiers()[0];

    //     $googleMerchantId = (!is_null($firstAccount->getMerchantId()) ? $firstAccount->getMerchantId() : $firstAccount->getAggregatorId());

    //     return $googleMerchantId;
    // }
}
