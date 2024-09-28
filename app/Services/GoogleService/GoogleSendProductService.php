<?php

namespace App\Services\GoogleService;

use App\Models\User;
use App\Models\Product;
use App\Helpers\ErrorLogger;
use App\Helpers\GoogleHelpers;
use App\Helpers\InfoLogger;
use App\Helpers\ApplicationProducts;
use Google\Service\ShoppingContent;
use Intervention\Image\Exception\NotFoundException;
use Google\Service\ShoppingContent\ProductsCustomBatchRequest;

class GoogleSendProductService
{
    public $storeId;

    public $lastUpdatedTimeForProduct;

    public function __construct(
        public User $user,
        public $userSettings,
        public $storeUrl,
        public $googleMerchantId,
        public $ApplicationSettingsCountry,
        public $ApplicationSettingsCurrency,
        public $additionalValues
    ) {
        $this->storeId = $user->store_id;
    }

    public function sendProductsToGoogle($ruleQuery, $old = false)
    {
        $allApplicationProducts =  (ApplicationProducts::getAllApplicationProducts($this->user, $ruleQuery));
        if ($allApplicationProducts == null) {

            return null;
        }

        foreach (array_chunk($allApplicationProducts, 100) as $products_chunked) {

            $databaseCreateSendProducts = [];
            $productsEntriesSendGoogleObjectArray = [];
            foreach ($products_chunked as $product) {
                foreach ($product['product_variants'] as $variant) {
                    $checkVariantExcluded = $this->checkVariantExcluded($variant);
                    if ($checkVariantExcluded) {
                        continue;
                    }
                    $databaseCreateSendProducts[] = $this->buildDatabaseProduct($product, $variant, $this->storeUrl);
                    $googleProductObject = $this->buildGoogleProductObject($product, $variant, $this->storeUrl);
                    $productsEntriesSendGoogleObjectArray[] = $googleProductObject;
                }
            }

            $this->lastUpdatedTimeForProduct = [
                'last_updated' => $product['product_date_update'] ?? '',
                'last_created' => $product['product_date_added'] ?? '', 'old' => $old
            ];

            $this->requestsToSend($databaseCreateSendProducts, $productsEntriesSendGoogleObjectArray);
        }
    }

    private function requestsToSend($databaseCreateSendProducts, $productsObjectArray)
    {
        $googleBatchResponse =  $this->sendPatchedProductToGoogle($productsObjectArray);
        if ($googleBatchResponse) {
            Product::upsert($databaseCreateSendProducts, ['variant_id']);
            GoogleHelpers::logGoogleResponse($googleBatchResponse);
            GoogleHelpers::updateGoogleSyncDetails($this->user, $this->lastUpdatedTimeForProduct);
        }
    }

    private function sendPatchedProductToGoogle($entries)
    {
        $googleClientService = new GoogleClientService($this->user);
        $googleClient = $googleClientService->makeGoogleClient();
        if (!$googleClient) {
            $th = throw new NotFoundException('Error make object of google client');
            ErrorLogger::logError($th,  $this->storeId);
            return false;
        }
        $googleShoppingService = new ShoppingContent($googleClient);
        $batchRequest = new ProductsCustomBatchRequest();
        $batchRequest->setEntries($entries);
        try {
            return $googleShoppingService->products->custombatch($batchRequest);
        } catch (\Throwable $th) {
            return false;
        }
    }

    private function getApplicationImage($variant, $product)
    {
        $productImage = '';
        $variantImage = $variant['variant_image'] ?? null;
        if (
            $variantImage !== null
        ) {
            $productImage = $variantImage;
        } else {
            if (isset($product['product_images'][0])) {
                $productImage = $product['product_images'][0];
            }
        }
        return $productImage;
    }

    private function checkVariantExcluded($variant)
    {
        $value = false;
        $variantExcluded = Product::where('user_store_id', $this->storeId)
            ->where('variant_id', $variant['variant_id'])->first();
        if (isset($variantExcluded) && $variantExcluded->is_excluded) {
            $value = true;
        }
        return $value;
    }

    private function buildDatabaseProduct($product, $variant)
    {
        return  [
            'user_store_id' => $this->storeId,
            'product_id' => $product['product_id'],
            'product_name' => $product['product_name'],
            'product_image' => $this->getApplicationImage($variant, $product),
            'variant_id' => $variant['variant_id'],
            'variant_option' => $variant['variant_option'] ?? '',
            'batch_id' => crc32($product['product_id'] . $variant['variant_id']),
            'offer_id' => md5($product['product_id'] . $variant['variant_id']),
            'status' => 'pending'
        ];
    }

    private function buildGoogleProductObject($product, $variant)
    {
        $googleEntryService = new GoogleMakeEntryProductToSend(
            $this->user,
            $product,
            $variant,
            $this->storeUrl,
            $this->userSettings,
            $this->additionalValues
        );

        return $googleEntryService->makeNewEntryForProduct(
            $product['product_id'],
            $variant['variant_id'],
            $this->googleMerchantId,
            $this->ApplicationSettingsCountry,
            $this->ApplicationSettingsCurrency,
            'insert'
        );
    }
}
