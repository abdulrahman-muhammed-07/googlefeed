<?php

namespace App\Http\Controllers\PluginControllers;

use Google\Client;
use App\Helpers\AccessToken;
use Illuminate\Http\Request;
use App\Helpers\ApiResponser;
use Google\Service\Exception;
use InvalidArgumentException;
use Google\Service\ShoppingContent;
use App\Http\Controllers\Controller;
use App\Helpers\GoogleBuildProductId;
use Illuminate\Support\Facades\Validator;

class ProductSearchGoogleController extends Controller
{
    public function ProductSearchGoogle(Request $request)
    {
        $storeId = $request->store_id;
        $googleAccessToken = $this->getGoogleAccessToken($storeId);
        if (!$googleAccessToken) {

            return "Please Login to Your Google with Merchant Account";
        }
        $googleClient = new Client();
        $service = new ShoppingContent($googleClient);
        $googleClient->setAccessToken($googleAccessToken);
        $googleClient->addScope(env('GOOGLE_SCOPE_FOR_API_AUTH'), env('GOOGLE_SCOPE_FOR_REFRESH_TOKEN'));
        $response = $service->accounts->authinfo();
        if (is_null($response->getAccountIdentifiers())) {
            throw new InvalidArgumentException(
                'Authenticated user has no access to any Merchant Center accounts'
            );
        }
        $firstAccount = $response->getAccountIdentifiers()[0];
        if (!is_null($firstAccount->getMerchantId())) {
            $merchantId = $firstAccount->getMerchantId();
        } else {
            $merchantId = $firstAccount->getAggregatorId();
        }
        $accessToken = AccessToken::getAccessToken($storeId);
        if ($accessToken == null) {
            return 'error';
        }
        $validator = Validator::make($request->all(), ['product_id' => 'required', 'variant_id' => 'required']);
        if ($validator->fails()) {
            return ApiResponser::fail($validator->errors()->first(), 404);
        }
        $product_id = $request->product_id;
        $variant_id = $request->variant_id;
        $product = $this->getProduct($product_id, $storeId);
        if ($product == true) {
            $product_variant_api = array_map(
                function ($product_variant) {
                    return $product_variant['variant_id'];
                },
                $this->getProduct($product_id, $storeId)['product_variants']
            );
            if (in_array($variant_id,  $product_variant_api) == false) {
                return ApiResponser::fail('Product Id & Variant Id do not match', 404);
            }
        }
        $buildProductId = GoogleBuildProductId::buildProductId(md5($product_id . $variant_id));
        try {
            $googleProduct = $service->products->get($merchantId, $buildProductId);
        } catch (Exception $th) {
            return ApiResponser::fail("The Product with ID : $product_id is Not Found on Google Merchant Center", 404);
        }
        return  $this->success(['product_id' => $product_id, 'variant_id' => $variant_id], 'This Product is on Google Merchant Center');
    }
}
