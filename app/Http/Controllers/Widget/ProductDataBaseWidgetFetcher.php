<?php

namespace App\Http\Controllers\Widget;

use App\Models\Product;

class ProductDataBaseWidgetFetcher
{
    public function fetchProduct($storeId, $variantId, $productId)
    {
        $product = Product::query()
            ->where('user_store_id', $storeId)
            ->where('variant_id', $variantId)
            ->where('product_id', $productId)
            ->first();

        return $product;
    }
}
