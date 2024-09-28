<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Oauth;
use Symfony\Component\Process\Exception\RuntimeException;

class GrpcErrorHandle
{
    public static function checkGrpcErrors(array $waitedArray, int $storeId)
    {
        $return = ['status' => true];
        if ($waitedArray[1]->code == 16) {
            $store = Oauth::where('user_store_id', '=', $storeId)->first() ?: '';
            if (!empty($store)) {
                $th = throw new RuntimeException($waitedArray[1]->details, 400);
                ErrorLogger::logError($th, $storeId);
                $user = User::where('store_id', '=', $storeId)->first();
                ReportAccessTokenExpiry::report($user);
            }
            $return =   ['status' => false, 'message' => $waitedArray[1]->details, 'code' => $waitedArray[1]->code];
        } elseif ($waitedArray[0] == null) {
            $message = $waitedArray[1]->details;
            $code = $waitedArray[1]->code;
            if ($message == 'no customers available' || $message == 'no products available' || $message == 'no product\/variant update is needed' || $message == 'no product variant update is needed') {
                $return =   ['status' => false];
            } else {
                $th = new RuntimeException($message, $code);
                ErrorLogger::logError($th, $storeId);
                $return =    ['status' => false, 'message' => $message, 'code' => $code];
            }
        } elseif ($waitedArray[0]->getFailure()) {
            $code = $waitedArray[0]->getCode();
            $message = $waitedArray[0]->getMessage();
            if ($message == 'no customers available' || $message == 'no products available' || $message == 'no product\/variant update is needed' || $message == 'no product variant update is needed') {
                $return = ['status' => false];
            } else {
                $th = new RuntimeException($message, $code);
                ErrorLogger::logError($th, $storeId);
                $return =  ['status' => false, 'message' => $message, 'code' => $code];
            }
        }
        return $return;
    }
}
