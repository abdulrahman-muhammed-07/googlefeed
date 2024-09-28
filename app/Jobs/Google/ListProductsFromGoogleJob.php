<?php

namespace App\Jobs\Google;

use Exception;
use App\Models\User;
use App\Models\Product;
use App\Models\UserSetting;
use App\Helpers\ErrorLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Google\Service\ShoppingContent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use App\Services\GoogleService\GoogleClientService;

class ListProductsFromGoogleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $storeId;
    public $timeout = 36000;
    public $googleMerchantId;
    public $googleClient;
    public $lastUpdatedTimeForProduct;
    public $googleClientService;
    public $googleShoppingService;
    public $nextPageToken = null;

    public function __construct(public User $user)
    {
        $this->storeId = $user->store_id;
    }

    public function handle()
    {
        // do {
        //     $this->googleClientService = new GoogleClientService($this->user);
        //     $this->googleClient = $this->googleClientService->makeGoogleClient();
        //     if (!$this->googleClient) {
        //         $th = new Exception('Error make object of google client');
        //         ErrorLogger::logError($th, $this->storeId);
        //         return false;
        //     }
        //     $this->googleShoppingService = new ShoppingContent($this->googleClient);
        //     $userSettings = UserSetting::where('user_store_id', $this->storeId)->first();
        //     if ($userSettings == null || $userSettings->merchant_id == null) {
        //         $th = new Exception('No Merchant Id set to this store');
        //         ErrorLogger::logError($th, $this->storeId);
        //         return false;
        //     }
        //     $this->googleMerchantId = $userSettings->merchant_id;
        //     $products = $this->googleShoppingService->productstatuses->listProductstatuses($this->googleMerchantId, ['pageToken' => $this->nextPageToken, 'maxResults' => 200]);
        //     $this->getResources($products);
        //     $this->nextPageToken = $products->getNextPageToken();
        // } while ($this->nextPageToken !== null);
    }

    private function getResources($products)
    {
        // foreach ($products->getResources() as $product) {
        //     $productGoogleOfferId = $product->getProductId();
        //     $productGoogleOfferId = str_replace('online:en:US:', '', $productGoogleOfferId);
        //     $errorArray = [];
        //     foreach ($product->getItemLevelIssues() as $issue) {
        //         if ($issue->getServability() == 'unaffected' || $issue->getServability() == 'demoted' || $issue->getServability() == 'approved') {
        //             continue;
        //         }
        //         $errorArray['time_stamp'] = date('Y-m-d H:i:s', time());
        //         $errorArray[$issue->getDescription()] = ['error' => $issue->getDestination(), 'status' => $issue->getServability(), 'message' => $issue->getDescription()];
        //     }
            // if (!empty($errorArray)) {
                // $product = Product::where('offer_id', '=', $productGoogleOfferId)->first();
                // $errorsArray = isset($product->google_error_array) ? ($product->google_error_array) : [];
                // if (!is_array($errorsArray)) {
                //     $errorsArray = [$errorsArray];
                // }
                // $errorsArray = array_merge($errorsArray, $errorArray);
                // $product->update(['google_error_array' => json_encode($errorsArray), 'status' => 'error']);
            // } else {
                // Product::query()->where('user_store_id',  $this->storeId)->where('offer_id', $productGoogleOfferId)->update(['google_error_array' => json_encode([]), 'status' => 'success',  'updated_at' => DB::raw('updated_at')]);
            // }
        // }
    }
}
