<?php

namespace App\Http\Controllers\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Widget\WidgetRequest;



class WidgetHandleController extends Controller
{
    public function getDataForWidget(WidgetRequest $request, ProductDataBaseWidgetFetcher $productFetcher)
    {
        $storeId = $request->store_id;
        $variantId = $request->variant_id;
        $productId = $request->product_id;

        try {

            $productFetcher = new ProductDataBaseWidgetFetcher;

            $product = $productFetcher->fetchProduct($storeId, $variantId, $productId);

            if (!$product) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Product not sent to google'
                    ]
                );
            }
        } catch (\Throwable $th) {

            return response()->json(
                [
                    'status' => 'error',
                    'message' => $th->getMessage(),
                    'extra' => $th->getCode()
                ]
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'data' => $product
            ]
        );
    }
}
