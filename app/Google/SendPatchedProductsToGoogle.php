<?php

namespace App\Google;

use App\Helpers\ErrorLogger;
use Google\Service\ShoppingContent\ProductsCustomBatchRequest;

class SendPatchedProductsToGoogle
{
    public function sendPatchedProductToGoogle($entries, $google_service, $storeId)
    {
        $batchRequest = new ProductsCustomBatchRequest();
        $batchRequest->setEntries($entries);
        try {
            $batchResponse = $google_service->products->custombatch($batchRequest);
            return ($batchResponse);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $storeId);
        }
    }
}
