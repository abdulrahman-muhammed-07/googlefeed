<?php

namespace App\Console\Commands\Google\Products;

use App\Models\User;
use Illuminate\Console\Command;
use App\Jobs\Google\DeleteDeletedProductsFromGoogleJob;

class DeleteDeletedProductsFromGoogleCommand extends Command
{
    protected $signature = 'deleteFromGoogle';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::get();
        if ($users != null) {
            foreach ($users as $user) {
                dispatch(new DeleteDeletedProductsFromGoogleJob($user));
            }
        }
    }
}
