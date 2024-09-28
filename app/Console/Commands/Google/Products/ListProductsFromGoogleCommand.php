<?php

namespace App\Console\Commands\Google\Products;

use App\Models\User;
use Illuminate\Console\Command;
use App\Jobs\Google\ListProductsFromGoogleJob;

class ListProductsFromGoogleCommand extends Command
{

    protected $signature = 'listFromGoogle';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::get();
        if ($users != null) {
            foreach ($users as $user) {
                if (
                    ($user->userSetting) != null && $user->userSetting->merchant_id != null  && $user->googleSetting->google_logged_in
                ) {
                    dispatch(new ListProductsFromGoogleJob($user));
                }
            }
        }
    }
}
