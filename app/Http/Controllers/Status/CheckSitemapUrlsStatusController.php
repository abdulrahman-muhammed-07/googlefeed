<?php

namespace App\Http\Controllers\Status;

// use App\Models\URLSTATUS;

class CheckSitemapUrlsStatusController
{
    public function checkStatus()
    {
        // try {
        //     $output = [];
        //     $urlStatus = URLSTATUS::query()->where('url', '!=', '')->where('updated_at', '>=', now()->subMinutes(6))->distinct('url')->get();
        //     $output = [];
        //     foreach ($urlStatus as $oneUrlStatus) {
        //         if ($oneUrlStatus->status == false) {
        //             $output[] = ['url' => $oneUrlStatus->url, 'status' => 'error', 'type' => $oneUrlStatus->type];
        //         }
        //     }
        //     return response()->json($output);
        // } catch (\Exception $th) {
        //     return response()->json(['error' => $th->getMessage(), 'status' => 'error']);
        // }
        // return response()->json($output);
    }
}
