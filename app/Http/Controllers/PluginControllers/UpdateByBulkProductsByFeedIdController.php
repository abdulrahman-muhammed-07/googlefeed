<?php

namespace App\Http\Controllers\PluginControllers;

use Google\Client;
use App\Models\UserSetting;
use App\Http\Controllers\Controller;
use Google\Service\ShoppingContent\ProductWeight;
use Google\Service\ShoppingContent\ProductShippingWeight;
use App\Http\Requests\Google\UpdateProductsByFeedIdRequest;
use Google\Service\ShoppingContent\ProductsCustomBatchRequestEntry;
use Google\Service\ShoppingContent\Price as Google_Service_Shopping_Content_Price;
use Google\Service\ShoppingContent\Product as Google_Service_Shopping_Content_Product;

class UpdateByBulkProductsByFeedIdController extends Controller
{
    public $storeId;

    public function indexAction(UpdateProductsByFeedIdRequest $request)
    {
        $this->storeId = $request->user()->store_id;
        $googleClient = $this->makeGoogleClient();
        $userSettings = UserSetting::where('user_store_id', $this->storeId)->first();
        if (!$userSettings || $userSettings->merchant_id == null) {
            return;
        }
        $googleMerchantId = $userSettings->merchant_id;
        $user = $request->user();
        $store_url = $this->getStoreWebsite($request->user());
        foreach ($request->products as $product) {
            $productWithVariant =   $this->getProductWithSpecificVariantFromApplication($user);
            $product = $productWithVariant['product'];
            $variant = $productWithVariant['variant'];
            $googleProduct = new Google_Service_Shopping_Content_Product();
            $offerId = md5($product['product_id'] . $variant['variant_id']);
            $googleProduct->setOfferId($offerId);
            $googleProduct->setId($variant['variant_id']);
            $googleProduct->setTitle($product['product_name']);
            $googleProduct->setDescription($product['product_description']);
            $googleProduct->setLink($store_url . '/product/' . $product['product_seo_url'] . '?utm_source=google&utm_medium=cpc');
            $productWeight = new ProductWeight();
            $productWeight->setValue($variant['variant_weight']);
            $productWeight->setUnit('lb');
            $googleProduct->setProductWeight($productWeight);
            $productShippingWeight = new ProductShippingWeight();
            $productShippingWeight->setValue($variant['variant_weight']);
            $productShippingWeight->setUnit('lb');
            $googleProduct->setShippingWeight($productShippingWeight);
            foreach ($product['product_images'] as $productImage) {
                $googleProduct->setImageLink(empty($variant['variant_image']) ?  $productImage : $variant['variant_image']);
            }
            if ((!empty($variant['variant_barcode'])) && is_numeric($variant['variant_barcode'])) {
                $googleProduct->setGtin($variant['variant_barcode']);
            }
            $googleProduct->setContentLanguage('en');
            $googleProduct->setTargetCountry('US');
            $googleProduct->setChannel('online');
            if ($product['product_status'] == true && $variant['variant_status'] == true) {
                $googleProduct->setAvailability('in stock');
            } else {
                $googleProduct->setAvailability('out of stock');
            }
            $googleProduct->setCondition(isset($product['product_custom_fields']['condition']) ? $product['product_custom_fields']['condition'] : 'new');
            $googleProduct->setGoogleProductCategory(isset($product['product_custom_fields']['google_product_category']) ? $product['product_custom_fields']['google_product_category'] : '');
            $googleProduct->setGtin(isset($product['product_custom_fields']['gtin']) ? $product['product_custom_fields']['gtin'] : '');
            if (!empty($product['product_options']) && isset($variant['variants'])) {
                foreach ($variant['variants'] as $value) {
                    $variant_option = $value;
                }
                $variants = array_combine($product['product_options'], $variant['variants']);
            } else {
                $variants = [];
            }
            $googleProduct->setItemGroupId(md5($product['product_id']));
            $googleProduct->setColor(isset($variants['color']) ? $variants['color'] : '');
            $googleProduct->setSizes(isset($variants['size']) ? $variants['size'] : '');
            $googleProduct->setPattern(isset($variants['pattern']) ? $variants['pattern'] : '');
            $googleProduct->setGender(isset($variants['gender']) ? $variants['gender'] : '');
            $googleProduct->setMaterial(isset($variants['material']) ? $variants['material'] : '');
            $price = new Google_Service_Shopping_Content_Price();
            $price->setValue(isset($variant['variant_price']) ? $variant['variant_price'] : 0.00);
            $price->setCurrency(isset($variant['variant_currency_code']) ? $variant['variant_currency_code'] : '');
            $googleProduct->setPrice($price);
            if ($variant['variant_on_sale'] == true) {
                $priceV = new Google_Service_Shopping_Content_Price();
                $priceV->setValue(isset($variant['variant_discount_price']) ? $variant['variant_discount_price'] : 0.00);
                $priceV->setCurrency(isset($variant['variant_currency_code']) ? $variant['variant_currency_code'] : '');
                $googleProduct->setSalePrice($priceV);
            }
            $entry = new ProductsCustomBatchRequestEntry();
            $entry->setMethod('insert');
            $entry->setBatchId(crc32($product['product_id'] . $variant['variant_id']));
            $entry->setProduct($googleProduct);
            $entry->setMerchantId($googleMerchantId);
            $entries[] = $entry;
        }
    }

    public function getProductWithSpecificVariantFromApplication()
    {
    }

    public function makeGoogleClient()
    {

        $googleClient = new Client();

        $googleAccessToken = $this->getGoogleAccessToken($this->storeId);

        if (!($googleAccessToken)) return false;

        $googleClient->setAccessToken($googleAccessToken);

        $googleClient->addScope(
            'https://www.googleapis.com/auth/content',
            'https://www.googleapis.com/auth/structuredcontent',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/plus.business.manage'
        );

        return $googleClient;
    }
}
