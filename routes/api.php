<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CodeMiddleware;
use App\Http\Controllers\Auth\CodeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\WidgetController;
use App\Http\Middleware\SessionTokenMiddleware;
use App\Http\Controllers\Settings\MerchantController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\SyncStatusController;
use App\Http\Controllers\Widget\WidgetHandleController;
use App\Http\Controllers\PluginControllers\HomeController;
use App\Http\Controllers\PluginControllers\WidgetDataController;
use App\Http\Controllers\PluginControllers\ProductSearchGoogleController;
use App\Http\Controllers\Status\CheckStatusController;

// use App\Http\Controllers\PluginControllers\ProductUploadGoogleController;
// use App\Http\Controllers\PluginControllers\ProductSearchApplicationController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('code', [CodeController::class, 'action'])->middleware(CodeMiddleware::class);
    Route::post('login', [LoginController::class, 'action'])->middleware(SessionTokenMiddleware::class);
    Route::post('widget', [WidgetController::class, 'validateWidget'])->middleware(SessionTokenMiddleware::class);
});
Route::middleware(['store.id'])->group(function () {
    // Route::post('send-one-product', [ProductUploadGoogleController::class, 'SendOneProductToGoogleWithProductId']);
    // Route::get('search-product-Application', [ProductSearchApplicationController::class, 'ProductSearchApplication']);
    Route::get('search-product-google', [ProductSearchGoogleController::class, 'ProductSearchGoogle']);
    Route::post('get-widget-data', [WidgetHandleController::class, 'getDataForWidget']);
    Route::get('all-products', [HomeController::class, 'index']);
    Route::post('update-exclude-status', [HomeController::class, 'excludeStatus']);
    Route::get('widget-data', [WidgetDataController::class, 'data']);
    Route::get('products-stats', [HomeController::class, 'stat']);
    Route::group(['prefix' => 'setting'], function () {
        Route::post('store-settings', [SettingsController::class, 'store']);
        Route::put('update-settings',  [SettingsController::class, 'update']);
        Route::get('show-settings',  [SettingsController::class, 'index']);
        Route::get('check-saved-default',  [SettingsController::class, 'CheckSavedInitSettings']);
        Route::get('choose-merchant-id',  [MerchantController::class, 'chooseMerchantId']);
        Route::get('check-merchant-id',  [MerchantController::class, 'checkMerchantId']);
        Route::get('check-logged-in',  [MerchantController::class, 'checkLoggedIn']);
        Route::put('update-sync-status',  [SyncStatusController::class, 'setSyncStatus']);
        Route::get('get-sync-status',  [SyncStatusController::class, 'getSyncStatus']);
    });
});

Route::get('status', [CheckStatusController::class, 'checkStatus']);
