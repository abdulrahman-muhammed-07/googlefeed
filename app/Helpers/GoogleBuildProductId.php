<?php

namespace App\Helpers;

class GoogleBuildProductId
{
    public static function buildProductId($googleProductId)
    {
        return  sprintf(
            '%s:%s:%s:%s',
            'online',
            'en',
            'US',
            $googleProductId
        );
    }
}
