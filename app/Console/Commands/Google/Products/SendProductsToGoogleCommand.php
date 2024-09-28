<?php

namespace App\Console\Commands\Google\Products;

use App\Models\User;
use Illuminate\Console\Command;
use App\Jobs\Google\SendProductsToGoogleJob;

class SendProductsToGoogleCommand extends Command
{
    protected $signature = 'syncToGoogle';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::get();
        if ($users != null) {
            foreach ($users as $user) {
                if ($user->userSetting && $user->googleSetting && $user->userSetting->merchant_id && $user->googleSetting->google_logged_in && $user->googleSetting->sync_status) {
                    dispatch(new SendProductsToGoogleJob($user));
                }
            }
        }
    }
}
