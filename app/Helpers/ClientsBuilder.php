<?php

namespace App\Helpers;

use Grpc;
use Google\Protobuf\GPBEmpty;
use Application\V1\Categories\CategoriesClient;
use Application\V1\Customers\CustomersClient;
use Application\V1\Menu\MenusClient;
use Application\V1\Products\ProductsClient;
use Application\V1\Settings\SettingsClient;
use Application\V1\Store_credit\StoreCreditClient;

class ClientsBuilder
{
    public static function getCredentials()
    {
        if (env("PLUGIN_ORIGIN") == 'Dev') {

            return [
                'credentials' => Grpc\ChannelCredentials::createInsecure()
            ];
        } else {

            return [
                'credentials' => Grpc\ChannelCredentials::createSsl()
            ];
        }
    }

    public static function getHostName()
    {
        if (env("PLUGIN_ORIGIN") == 'Dev') {

            return '192.168.1.99:7000';
        } else {

            return 'api.Application.com:443';
        }
    }

    public static function getStoreCreditClient()
    {
        return new StoreCreditClient(self::getHostName(), self::getCredentials());
    }

    public static function getCustomersClient()
    {
        return new CustomersClient(self::getHostName(), self::getCredentials());
    }

    public static function getMenusClient()
    {
        return new MenusClient(self::getHostName(), self::getCredentials());
    }

    public static function getProductsClient()
    {
        return new ProductsClient(self::getHostName(), self::getCredentials());
    }

    public static function getSettingsClient()
    {
        return new SettingsClient(self::getHostName(), self::getCredentials());
    }

    public static function getCategoriesClient()
    {
        return new CategoriesClient(self::getHostName(), self::getCredentials());
    }

    public static function getSettingsRequest(): GPBEmpty
    {
        return  new GPBEmpty;
    }
}
