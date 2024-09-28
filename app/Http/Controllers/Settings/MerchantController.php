<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ErrorLogger;
use Illuminate\Http\Request;
use App\Helpers\ApiResponser;
use App\Helpers\MerchantHelper;
use App\Http\Controllers\Controller;

class MerchantController extends Controller
{
    public function __construct(public MerchantHelper $merchantHelper)
    {
        //
    }

    public function chooseMerchantId(Request $request)
    {
        try {
            $response = $this->merchantHelper->chooseMerchantId($request, true);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            $response = ApiResponser::fail($th);
        }
        return $response;
    }

    public function checkMerchantId(Request $request)
    {
        try {
            $response = $this->merchantHelper->checkMerchantId($request);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            $response = ApiResponser::fail($th);
        }
        return $response;
    }

    public function checkLoggedIn(Request $request)
    {
        try {
            $response =  $this->merchantHelper->checkLoggedIn($request);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            $response = ApiResponser::fail($th);
        }
        return $response;
    }
}
