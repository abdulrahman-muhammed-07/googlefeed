<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\AccessTokenJob;
use Illuminate\Console\Command;

class AccessTokenCommand extends Command
{
    protected $signature = 'refresh:access';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::has('oauth')->get();
        foreach ($users as $user) {
            dispatch(new AccessTokenJob($user));
        }
    }
}
