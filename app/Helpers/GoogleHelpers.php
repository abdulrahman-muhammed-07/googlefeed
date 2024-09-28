<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\SyncDetails;

class GoogleHelpers
{
    public static function getProductCategorySeoUrl($product, $user)
    {
        $getProductWithId = new ApplicationRequests($user);

        $result = $getProductWithId->getProductCategories($product['product_id']);

        return $getProductWithId->getCategoryUrl($result);
    }

    public static function getLastUpdatedData($user, $old = false)
    {
        $typeId = md5($user->store_id . 'products');

        if ($old) {
            $typeId = md5($user->store_id . 'products_old');
        }

        $lastUpdated = SyncDetails::select('last_updated')->where('sync_detail_id', '=', $typeId)->first();

        $lastUpdatedValue = 1;

        if ($lastUpdated != null) {

            $lastUpdatedValue = $lastUpdated->last_updated;
        }

        return $lastUpdatedValue;
    }

    public static function updateGoogleSyncDetails($user, $lastUpdatedTimeForProduct)
    {
        $syncDetailId = $lastUpdatedTimeForProduct['old']
            ? md5($user->store_id . 'products_old') : md5($user->store_id . 'products');

        $syncType = $lastUpdatedTimeForProduct['old'] ? 'products_old' : 'products';

        $lastUpdateTime = $lastUpdatedTimeForProduct['old'] ? time() + 2160000 : time();

        SyncDetails::updateOrCreate(
            [
                'sync_detail_id' => $syncDetailId,
                'sync_store_id' => $user->store_id,
            ],
            [
                'sync_type' => $syncType,
                'last_created' => (int) ($lastUpdatedTimeForProduct['last_created']) ?? 0,
                'last_updated' => $lastUpdateTime,
                'last_sync' => time()
            ]
        );
    }

    public static function logGoogleResponse($batchResponse)
    {
        if (isset($batchResponse)) {

            foreach ($batchResponse->getEntries() as $entry) {

                $productBatchId = $entry->getBatchId();

                if (!empty($entry->getErrors())) {

                    $errorsArray = [];

                    foreach ($entry->getErrors()->getErrors() as $error) {

                        $errorsArray[$error->message] = [
                            'reason' => $error->reason,
                            'message' => $error->message
                        ];
                    }

                    Product::where('batch_id', '=', $productBatchId)->update([
                        'google_error_array' => json_encode($errorsArray),
                        'status' => 'error'
                    ]);
                } else {

                    Product::where('batch_id', '=', $productBatchId)->update([
                        'status' => 'success',
                        'google_error_array' => null,

                    ]);
                }
            }
        }
    }
}
