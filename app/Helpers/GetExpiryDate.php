<?php

namespace App\Helpers;

use Carbon\Carbon;

class GetExpiryDate
{
    public static function get($user)
    {
        $timeStamp = isset($user->oauth->expiry_date) ? $user->oauth->expiry_date : 0;

        if ($timeStamp == 0) {
            return 0;
        }

        $date = Carbon::createFromTimestamp($timeStamp);

        $now = Carbon::now();

        $diff = $date->diffForHumans($now, [
            'parts' => 3,
            'join' => true,
        ]);

        return [$diff, $timeStamp];
    }
}
