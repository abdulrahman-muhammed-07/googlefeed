<?php

namespace App\Helpers;

use App\Models\User;
use Application\V1\Products\ProductVariant;
use Application\V1\Products\ProductInfoRequest;
use Application\V1\Categories\CategoryInfoRequest;
use Application\V1\Products\ProductIDByVariantRequest;

class ApplicationRequests
{
    public $productClient;
    public $productInfoRequest;
    public $categoryClient;
    public $categoryRequest;
    public $productByVariantIDRequest;

    public function __construct(public User $user)
    {
        $this->productClient = ClientsBuilder::getProductsClient();

        $this->categoryClient = ClientsBuilder::getCategoriesClient();

        $this->productInfoRequest = new ProductInfoRequest();

        $this->productByVariantIDRequest = new ProductIDByVariantRequest();

        $this->categoryRequest = new CategoryInfoRequest();
    }

    public function getProductById($productId)
    {
        $this->productInfoRequest->setProductId($productId);

        $metaData =  array(
            'authorization' => array('Bearer ' . AccessToken::getAccessToken($this->user->store_id)),
            'x-client-id' => array((string)$this->user->store_id)
        );

        $initResponse = $this->productClient->GetProductByID(
            $this->productInfoRequest,
            $metaData
        );

        $response = $initResponse->wait();

        if (!GrpcErrorHandle::checkGrpcErrors($response, $this->user->store_id)['status']) {

            return false;
        }

        return $response[0];
    }

    public function getProductByIdWithVariantId($productId, $variantId)
    {
        $productVariantRequest = new ProductVariant;

        $productVariantRequest = $productVariantRequest->setProductId($productId)->setVariantId($variantId);

        $this->productByVariantIDRequest->setProductVariantRequest([$productVariantRequest]);

        $initResponse = $this->productClient->GetProductByIDWithVariantArray(
            $this->productByVariantIDRequest,
            MetaData::get($this->user->store_id)
        );

        $response = $initResponse->wait();

        $checkGrpc = GrpcErrorHandle::checkGrpcErrors($response, $this->user->store_id);

        if ($checkGrpc['status'] == false) {

            return $checkGrpc;
        }

        return $response[0];
    }

    public function getProductByIdWithVariantIdAsArray($productId, $variantId)
    {
        $resultBody = $this->getProductByIdWithVariantId($productId, $variantId);

        if (
            (is_array($resultBody) && isset($resultBody['status']) && !$resultBody['status'])
            || $resultBody->getProductsListing() == null
        ) {

            return  $resultBody;
        }

        $product = $resultBody->getProductsListing()->getProductsListing();

        $product = $product[0];

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
                'variant_image' => $variant->getVariantImage() != null ? $variant->getVariantImage()->getImageUrl() : ($productImages[0] ?? ''),
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
                $completeVariant['variant_option_values'][] = $value;
            }

            $productVariant = $completeVariant;
            $completeVariant = [];
        }

        $productTags = [];

        foreach ($product->getProductTags() as $productTag) {
            $productTags[] = $productTag;
        }

        return [
            'product_id' => $product->getProductId(),
            'product_name' => $product->getProductName(),
            'product_model' => $product->getProductModel(),
            'product_status' => $product->getProductStatus(),
            'product_description' => strip_tags($product->getProductDescription()),
            'product_images' => $productImages,
            'product_virtual' => $product->getProductVirtual(),
            'product_brand' => $product->getProductBrand(),
            'product_model' => $product->getProductModel(),
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
            'product_variant' => $productVariant,
            'product_total_qty' => $product->getProductTotalQty(),
            'product_on_sale' => $product->getProductOnSale(),
            'product_tags' => $productTags,
            'product_type' => $product->getProductType(),
            'product_date_update' => $product->getProductDateUpdate(),
            'product_vendor' => $product->getProductVendor(),
        ];
    }

    public function getCategoryById($categoryId)
    {
        $this->categoryRequest->setCategoryId($categoryId);

        $initResponse = $this->categoryClient->GetCategoryByID(
            $this->categoryRequest,
            MetaData::get($this->user->store_id)
        );

        $response = $initResponse->wait();

        $checkGrpc = GrpcErrorHandle::checkGrpcErrors($response, $this->user->store_id);

        if (!$checkGrpc['status']) {

            return $checkGrpc;
        }

        return $response[0];
    }

    public function getProductCategories($productId)
    {
        $result =  $this->getProductById($productId)->getProduct();

        $productRules = ($result->getProductRules());

        $categories = [];

        foreach ($productRules as $rule) {

            $categoryId = $rule->getCategoryId();

            if (!$categoryId) {
                continue;
            }

            $categories[] = $categoryId;
        }

        return $categories;
    }

    public function getCategoryUrl($categories)
    {
        $seoUrls = [];

        foreach ($categories as $categoryId) {

            $categoryResult =  $this->getCategoryById($categoryId);

            $categorySeoUrl = $categoryResult->getCategory()->getCategorySeoUrl();

            if (!$categorySeoUrl) {

                continue;
            }

            $seoUrls[] = $categorySeoUrl;
        }

        return $seoUrls;
    }
}
