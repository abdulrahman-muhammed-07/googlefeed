<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Helpers\ApiResponser;
use App\Jobs\Google\SendProductsToGoogleJob;

class SyncStatusController
{
    public function setSyncStatus(Request $request)
    {
        try {
            $validatedValues = $request->validate(['sync_status' => 'required|boolean']);
            $request->user()->googleSetting()->update(['sync_status' => $validatedValues['sync_status']]);
            if ($validatedValues['sync_status']) {
                dispatch(new SendProductsToGoogleJob($request->user()));
            }
            return   ApiResponser::success(['status' => 'success']);
        } catch (\Throwable $th) {
            return ApiResponser::fail($th);
        }
    }

    public function getSyncStatus(Request $request)
    {
        try {
            $syncStatus = $request->user()->googleSetting()->first()->value('sync_status');
            return ApiResponser::success(['sync_status' => $syncStatus]);
        } catch (\Throwable $th) {
            return  ApiResponser::fail($th);
        }
    }
}
