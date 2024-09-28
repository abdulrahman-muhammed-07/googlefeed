<?php

namespace App\Jobs\Google;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\UserSetting;
use App\Helpers\ErrorLogger;
use Illuminate\Bus\Queueable;
use Google\Service\ShoppingContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\GoogleService\GoogleClientService;

class DeleteProductFromGoogleWhenExcludedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $storeId;

    public $variantId;

    public $productId;

    public $lastUpdatedTimeForProduct;

    public $googleClientService;

    public $googleShoppingService;

    public function __construct(public User $user, $productId, $variantId)
    {
        $this->storeId = $user->store_id;
        $this->productId = $productId;
        $this->variantId = $variantId;
    }

    public function handle()
    {
        $this->googleClientService = new GoogleClientService($this->storeId);
        $googleClient = $this->googleClientService->makeGoogleClient();
        if (!$googleClient) {
            $th = new Exception('Error make object of google client');
            ErrorLogger::logError($th, $this->storeId);
            return false;
        }
        $googleShoppingService = new ShoppingContent($googleClient);
        $productId = Product::where('product_id', $this->productId)->where('variant_id', $this->variantId)->first();
        $offerId = $productId->offer_id;
        $merchantId = $this->getGoogleMerchantId();
        if (!isset($merchantId)) {
            return false;
        }
        try {
            $googleShoppingService->products->delete($merchantId, 'online:en:US:' . $offerId);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $this->storeId);
        }
    }

    public function getGoogleMerchantId()
    {
        $userSettings = UserSetting::where('user_store_id', $this->storeId)->first();
        if ($userSettings == null || $userSettings->merchant_id == null) {
            $th = new Exception('No Merchant Id set to this store');
            ErrorLogger::logError($th, $this->storeId);
            return false;
        }
        return $userSettings->merchant_id;
    }
}
