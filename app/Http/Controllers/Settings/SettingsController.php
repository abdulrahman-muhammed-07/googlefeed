<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ErrorLogger;
use Illuminate\Http\Request;
use App\Helpers\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Http\Requests\Settings\ShowSettingRequest;
use App\Http\Requests\Settings\StoreSettingsRequest;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Repositories\SettingsRepository\SettingsRepository;

class SettingsController extends Controller
{
    public function index(ShowSettingRequest $request,  SettingsRepository $settingsRepository)
    {
        try {
            $response = SettingResource::collection($settingsRepository->index($request));
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            $response = ApiResponser::fail($th);
        }
        return $response;
    }

    public function store(StoreSettingsRequest $request, SettingsRepository $settingsRepository)
    {
        try {
            $response = new SettingResource($settingsRepository->store($request)->user()->userSetting);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            $response = ApiResponser::fail($th);
        }
        return $response;
    }

    public function update(UpdateSettingsRequest $request, SettingsRepository $settingsRepository)
    {
        try {
            return $settingsRepository->update($request);
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $request->user()->store_id);
            return ApiResponser::fail($th);
        }
    }

    public function CheckSavedInitSettings(Request $request)
    {
        return response()->json(['status' => $request->user()->googleSetting->saved_init_settings ?? 0]);
    }
}
