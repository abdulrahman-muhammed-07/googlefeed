<?php

namespace App\Jobs\Google;

use App\Models\User;
use App\Models\Product;
use App\Models\syncDetails;
use App\Helpers\GoogleClient;
use Google\Service\Exception;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use App\Helpers\ApplicationProducts;
use Google\Service\ShoppingContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Google\Service\ShoppingContent\ProductsCustomBatchRequest;
use Google\Service\ShoppingContent\ProductsCustomBatchRequestEntry;

class DeleteDeletedProductsFromGoogleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $storeId;

    public $timeout = 36000;

    public function __construct(public User $user)
    {
        $this->storeId = $user->store_id;
    }

    public function handle()
    {
        $googleClient =  $googleClient = GoogleClient::makeClient($this->user);
        $service = new ShoppingContent($googleClient);
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
        $lastUpdated = syncDetails::select('last_updated')->where('id', '=', md5($this->storeId . 'products'))->first();
        $lastUpdatedValue = 1;
        if ($lastUpdated != null) {
            $lastUpdatedValue = $lastUpdated->last_updated;
        }
        $entries = [];
        $deletedProducts = ApplicationProducts::getDeletedProducts($this->storeId, $lastUpdatedValue);
        if ($deletedProducts == null) {
            return false;
        }
        foreach ($deletedProducts as $product) {
            if (isset($product->product_id) && isset($product->variants_ids)) {
                $productId = $product->product_id;
                foreach ($product->variants_ids as $variant_id) {
                    $product = Product::where('variant_id', '=', $variant_id)->first();
                    if ($product != null) {
                        $offerId = $product->offer_id;
                    } else {
                        continue;
                    }
                    try {
                        $entry = new ProductsCustomBatchRequestEntry();
                        $entry->setMethod('delete');
                        $entry->setBatchId(crc32($productId . $variant_id));
                        $entry->setProductId($productId);
                        $entry->setMerchantId($merchantId);
                        $entries[] = $entry;
                        $batchRequest = new ProductsCustomBatchRequest();
                        $batchRequest->setEntries($entries);
                        $batchResponses = $service->products->custombatch($batchRequest);
                        $errors = 0;
                        foreach ($batchResponses->entries as $entry) {
                            if (!empty($entry->getErrors())) {
                                $errors++;
                            }
                        }
                        Product::where('product_id', $productId)->delete();
                    } catch (Exception $th) {
                        return false;
                    }
                }
            }
        }
        $batchRequest = new ProductsCustomBatchRequest();
        $batchRequest->setEntries($entries);
        $batchResponses = $service->products->custombatch($batchRequest);
        foreach ($batchResponses->entries as $entry) {
            if (!empty($entry->getErrors())) {
                foreach ($entry->getErrors()->getErrors() as $error) {
                    return false;
                }
            }
        }
    }
}
