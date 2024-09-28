<?php

namespace App\Http\Controllers\Status;

use App\Models\Oauth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class CheckStatusController
{
    public function checkStatus()
    {
        try {
            $output = [];
            $output['jobs_services'] =  $this->checkSystemctl();
            $output['DataBase_connection'] =  $this->checkDataBase();
            $output['access_tokens'] =  $this->checkAccessToken();
        } catch (\Exception $th) {
            return response()->json(['error' => $th->getMessage(), 'status' => 'error']);
        }
        return response()->json($output);
    }

    public function checkSystemctl()
    {
        $output = [];

        $scheduleProcess = Process::fromShellCommandLine('systemctl status gpfeed_schedule');
        $scheduleProcess->run();
        $output['schedule'] = (!str_contains($scheduleProcess->getOutput(), 'Active: active (running)') || str_contains($scheduleProcess->getOutput(), 'FAIL')) ? 'error' : 'ok';

        $queueProcess = Process::fromShellCommandLine('systemctl status gpfeed_queue');
        $queueProcess->run();
        $output['queue'] = !str_contains($queueProcess->getOutput(), 'Active: active (running)' || str_contains($queueProcess->getOutput(), 'FAIL')) ? 'error' : 'ok';

        return ($output);
    }

    public function checkDataBase()
    {
        DB::connection()->getPDO();
        return  DB::connection()->getDatabaseName() ? 'ok' : 'error';
    }

    public function checkAccessToken()
    {
        $output = [];
        $accessTokens = $this->getAccessToken();
        foreach ($accessTokens as $oneAccessToken) {
            if (!isset($oneAccessToken['access_token']) || time() > (int) $oneAccessToken['expiry_date'] + 50) {
                $output['user_store_id_' . $oneAccessToken['user_store_id']] = 'error';
            } else {
                $output['user_store_id_' . $oneAccessToken['user_store_id']] = 'ok';
            }
        }
        return ($output);
    }

    public function getAccessToken()
    {
        return Oauth::select('user_store_id', 'access_token', 'expiry_date',)->get()->toArray();
    }
}
