<?php

namespace App\Helpers;

use Application\V1\Products\RuleSortBy;
use Application\V1\Products\ProductInfoUpdateInt;
use Application\V1\Products\ProductListingInfo;

class ApplicationProducts
{
    private static function getAllApplicationProductsRequest(int $storeId,  $ruleQuery, int $displayResult = 50)
    {
        $productClient = ClientsBuilder::getProductsClient();

        $newPit = '';
        $request = new ProductListingInfo();

        $request->setRuleQuery($ruleQuery);

        $sortBy = new RuleSortBy();

        $sortBy->setColumnName('product.last.update');

        $sortBy->setSortOrder(0);

        $arrayOfSortRules = [$sortBy];

        $request->setSortBy($arrayOfSortRules);

        $request->setDisplayResult($displayResult);

        $productsBuiltArray = [];

        $counter = 0;
        while (true) {
            $request->setPit($newPit);

            $productArray = $productClient->GetProductListingByID($request, MetaData::get($storeId));

            $productArray = $productArray->wait();

            if (($counter >= 1) || ($productArray[0]->getMessage() == 'no products available')) {

                yield $productsBuiltArray;

                $shouldBreak = ($counter == 0) || ($productArray[0]->getMessage() == 'no products available');

                if ($shouldBreak) {
                    break;
                }

                $productsBuiltArray = [];
            }

            if (!GrpcErrorHandle::checkGrpcErrors($productArray, $storeId)['status']) {

                return false;
            }

            $counter++;

            $productResultResponse = $productArray[0];

            $newPit = $productResultResponse->getProductsListing()->getPit();

            $products = $productResultResponse->getProductsListing()->getProductsListing();

            foreach ($products as $product) {

                $productFromApplicationArray = self::getProductArrayOfApplication($product);

                $productsBuiltArray[] = $productFromApplicationArray;
            }
        }
    }

    public static function getDeletedProducts(int $storeId, int $productLastUpdate = 1)
    {
        $productClient = ClientsBuilder::getProductsClient();

        $request = new ProductInfoUpdateInt();

        $request->setValue($productLastUpdate);

        $productApplicationResponse = $productClient->GetDeletedProductInfo($request,  MetaData::get($storeId));

        $productApplicationResponse = $productApplicationResponse->wait();

        if (!GrpcErrorHandle::checkGrpcErrors($productApplicationResponse, $storeId)['status']) {
            return false;
        }

        $products = $productApplicationResponse[0]->getProductsListing()->getProductsListing();

        $deletedProductsArrayFromApplication = [];

        foreach ($products as $product) {

            $deletedProductId = $product->getProductId();

            if (count($product->getProductVariants()) == 0) {

                $deletedProductsArrayFromApplication[] = (object)['product_id' => $deletedProductId];
            } else {

                $variantIds = [];

                foreach ($product->getProductVariants() as $variant) {

                    $variantIds[] = $variant->getVariantId();
                }

                $deletedProductsArrayFromApplication[] =
                    ['product_id' => $deletedProductId, 'variants_ids' => $variantIds];
            }
        }

        if (isset($product)) {

            $deletedProductsArrayFromApplication[] = (object)['last_updated' => $product->getProductDateUpdate()];
        }

        return $deletedProductsArrayFromApplication;
    }

    private static function getProductArrayOfApplication($product)
    {
        $productImages = [];
        foreach ($product->getProductImages() as $image) {
            $productImages[] = $image->getImageUrl();
        }

        $productOptions = [];
        foreach ($product->getProductOption() as $option) {
            $productOptions[] = $option;
        }

        $productCustomFields = [];
        foreach ($product->getProductCustomFields() as $key => $custom_field) {
            $productCustomFields[$key] = $custom_field;
        }

        $productVariants = self::getProductVariantsArrayOfApplication($product, $productImages);

        $productTags = [];
        foreach ($product->getProductTags() as $product_tag) {
            $productTags[] = $product_tag;
        }

        return [
            'product_id' => $product->getProductId(),
            'product_name' => $product->getProductName(),
            'product_model' => $product->getProductModel(),
            'product_brand' => $product->getProductBrand(),
            'product_status' => $product->getProductStatus(),
            'product_description' => strip_tags($product->getProductDescription()),
            'product_images' => $productImages,
            'product_virtual' => $product->getProductVirtual(),
            'product_allow_recurring' => $product->getProductAllowRecurring(),
            'product_date_added' => $product->getProductDateAdded(),
            'product_date_available' => $product->getProductDateAvailable(),
            'product_weight' => $product->getProductWeight(),
            'product_dim_type' => $product->getProductDimType(),
            'product_sold_qty' => $product->getProductSoldQty(),
            'product_number_of_reviews' => $product->getProductNumberOfReviews(),
            'product_stars_average' => $product->getProductStarsAverage(),
            'product_order_min' => $product->getProductOrderMin(),
            'product_order_max' => $product->getProductOrderMax(),
            'product_order_units' => $product->getProductOrderUnits(),
            'product_seo_title' => $product->getProductSeoTitle(),
            'product_seo_description' => $product->getProductSeoDescription(),
            'product_seo_url' => $product->getProductSeoUrl(),
            'product_options' => $productOptions,
            'product_custom_fields' => $productCustomFields,
            'product_variants' => $productVariants,
            'product_total_qty' => $product->getProductTotalQty(),
            'product_on_sale' => $product->getProductOnSale(),
            'product_tags' => $productTags,
            'product_type' => $product->getProductType(),
            'product_date_update' => $product->getProductDateUpdate(),
            'product_vendor' => $product->getProductVendor()
        ];
    }

    private static function getProductVariantsArrayOfApplication($product, $productImages)
    {
        $productVariants = [];

        foreach ($product->getProductVariants() as $variant) {

            $variantCustomFields = [];
            foreach ($variant->getVariantCustomFields() as $key => $custom_field) {
                $variantCustomFields[$key] = $custom_field;
            }

            $completeVariant = [
                'variant_id' => $variant->getVariantId(),
                'variant_status' => $variant->getVariantStatus(),
                'variant_price' => ($variant->getVariantPrice()) != null
                    ? str_replace('$', '', $variant->getVariantPrice()->getDecimal()) : '',
                'variant_currency_code' => ($variant->getVariantPrice()) != null
                    ?  $variant->getVariantPrice()->getCurrencyCode() : '',
                'variant_discount_price' => ($variant->getVariantDiscountPrice()) != null
                    ? str_replace('$', '', $variant->getVariantDiscountPrice()->getDecimal()) : '',
                'variant_whole_sale_price' => ($variant->getVariantWholesalePrice()) != null
                    ? $variant->getVariantWholesalePrice()->getAmount() : '',
                'variant_sale_end' => $variant->getVariantSaleEndUnwrapped(),
                'variant_limited_qty' => $variant->getVariantLimitedQty(),
                'variant_qty' => $variant->getVariantQty(),
                'variant_allow_backorder' => $variant->getVariantAllowBackorder(),
                'variant_weight' => $variant->getVariantWeight(),
                'variant_sold_qty' => $variant->getVariantSoldQty(),
                'variant_barcode' => $variant->getVariantBarcode(),
                'variant_sku' => $variant->getVariantSku(),
                'variant_image' => $variant->getVariantImage() != null
                    ? $variant->getVariantImage()->getImageUrl() : ($productImages[0] ?? ''),
                'variant_length' => $variant->getVariantLength(),
                'variant_width' => $variant->getVariantWidth(),
                'variant_height' => $variant->getVariantHeight(),
                'variant_sale_start' => $variant->getVariantSaleStartUnwrapped(),
                'variant_on_sale' => $variant->getVariantOnSale(),
                'available_inventory' => $variant->getAvailableInventory(),
                'variant_custom_fields' => $variantCustomFields,
            ];

            foreach ($variant->getVariantOptionValue() as $value) {
                $value = addslashes($value);
                $completeVariant['variants'][] = $value;
            }

            $productVariants[] = $completeVariant;
            $completeVariant = [];
        }

        return $productVariants;
    }

    public static function getAllApplicationProducts($user, $ruleQuery)
    {
        $ApplicationProductToGet = self::getAllApplicationProductsRequest($user->store_id, $ruleQuery);

        if ($ApplicationProductToGet == null || $ApplicationProductToGet == []) {

            return null;
        }

        $allApplicationProducts = iterator_to_array($ApplicationProductToGet);

        return call_user_func_array('array_merge', $allApplicationProducts);
    }
}
