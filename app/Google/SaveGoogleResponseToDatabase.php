<?php

namespace App\Google;

use App\Models\Product;

class SaveGoogleResponseToDatabase
{
    public function setPatchResponseToDatabase($batchResponse)
    {
        $count = 0;
        if (isset($batchResponse)) {
            foreach ($batchResponse->entries as $entry) {
                if (!empty($entry->getErrors())) {
                    $count++;
                    $product = $entry->getBatchId();
                    $errors_array = [];
                    foreach ($entry->getErrors()->getErrors() as $error) {
                        $errors_array[] = $error->message;
                    }
                    $updateDatabase =  Product::where('batch_id', '=', $product)->update([
                        'error_log' => json_encode($errors_array)
                    ]);
                }
            }
        }
        return $count;
    }
}
