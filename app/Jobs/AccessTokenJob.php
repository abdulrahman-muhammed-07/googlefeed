<?php

namespace App\Jobs;

use App\Models\User;
use App\Helpers\ErrorLogger;
use App\Helpers\InfoLogger;
use Illuminate\Bus\Queueable;
use App\Http\Services\AuthService;
use Illuminate\Queue\SerializesModels;
use App\Helpers\ReportAccessTokenExpiry;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AccessTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $storeId;
    private $authService;

    public function __construct(public User $user)
    {
        $this->storeId = $user->store_id;
    }

    public function handle()
    {
        $this->authService = new AuthService();
        $this->updateAccessToken($this->storeId);
    }

    private function updateAccessToken($storeId)
    {
        try {
            $newAccessToken = $this->authService->refreshAccessToken($this->user->store_id);
            if (isset($newAccessToken) && $newAccessToken['access_token'] != null) {
                $this->user->oauth->updateOrCreate(
                    ['user_store_id' =>  $this->user->store_id],
                    ['access_token' => $newAccessToken['access_token'], 'refresh_token' => $newAccessToken['refresh_token'], 'expiry_date' => $newAccessToken['expiry']]
                );
            } else {
                throw new \Exception('Failed to refresh access token', 500);
            }
        } catch (\Throwable $th) {
            ReportAccessTokenExpiry::report($this->user);
            ErrorLogger::logError($th, $storeId);
        }
    }
}
