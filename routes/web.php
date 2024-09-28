<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthForGoogle\GoogleAuthController;
use App\Http\Controllers\PluginControllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// must be web here do not move 
Route::group(['prefix' => 'google'], function () {
    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});


Route::get('/uninstall', [HomeController::class, 'uninstall']);
