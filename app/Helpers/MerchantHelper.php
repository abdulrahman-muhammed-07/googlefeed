<?php

namespace App\Helpers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Google\Service\ShoppingContent;
use App\Http\Controllers\Controller;
use App\Services\GoogleService\GoogleClientService;

class MerchantHelper extends Controller
{
    public function checkRequestMerchantIdMatches($request)
    {
        $identifiers = $this->chooseMerchantId($request, false);

        return isset($identifiers['merchant_ids']) && in_array($request->merchant_id, $identifiers['merchant_ids']);
    }

    public function chooseMerchantId(Request $request, $json = true)
    {
        $googleClientService = new GoogleClientService($request->user());

        $googleClient = $googleClientService->makeGoogleClient();

        if (!$googleClient) {

            $response =  ['error' => 'Please log in to google first to be able to get the merchant id.'];
        } else {

            $response = $this->getMerchantIdsResponse($googleClient);
        }

        if (!$json) {
            return $response;
        }

        return response()->json($response, ($request->user() ? 200 : 400));
    }

    private function getMerchantIdsResponse($googleClient)
    {
        $googleService = new ShoppingContent($googleClient);

        $response = $googleService->accounts->authinfo();

        if (!isset($response) || is_null($response->getAccountIdentifiers())) {
            return [
                'error' => 'No merchant id.'
            ];
        }

        $identifiers = $response->getAccountIdentifiers();

        if ($identifiers) {

            $merchantIds = [];

            foreach ($identifiers as $identifier) {
                $merchantIds[] = $identifier->getMerchantId();
            }

            $response =  [
                'merchant_ids' => $merchantIds
            ];
        } else {

            $response =  [
                'error' => 'No merchant ids to select from.'
            ];
        }

        return $response;
    }

    public function checkMerchantId(Request $request)
    {
        $userStoreId = $request->user()->store_id;

        $userSettings = UserSetting::where('user_store_id', $userStoreId)->first();

        if (!$userSettings || $userSettings->merchant_id === null || !is_int($userSettings->merchant_id)) {

            return response()->json([
                'message' => 'No valid merchant ID found. Please enter the correct merchant ID.',
                'status' => false
            ], 404);
        }

        return response()->json([
            'message' => 'Merchant ID found',
            'status' => true
        ], 200);
    }

    public function checkLoggedIn(Request $request)
    {
        return response()->json(
            [
                'logged' => $request->user()->googleSetting->google_logged_in ?? 0
            ]
        );
    }
}
