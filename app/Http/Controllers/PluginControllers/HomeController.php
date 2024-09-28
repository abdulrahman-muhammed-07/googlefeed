<?php

namespace App\Http\Controllers\PluginControllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\Oauth;
use App\Models\State;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\SyncDetails;
use App\Models\UserSetting;
use App\Helpers\ErrorLogger;
use Illuminate\Http\Request;
use App\Helpers\ApiResponser;
use App\Models\GoogleSetting;
use App\Http\Controllers\Controller;
use GuzzleHttp\Psr7\Request as GuzzleHttp;
use App\Jobs\Google\SendProductsToGoogleJob;
use App\Jobs\Google\SendProductToGoogleBackJob;
use Savannabits\PrimevueDatatables\PrimevueDatatables;
use App\Jobs\Google\deleteDeletedProductsFromGoogleJob;
use App\Jobs\Google\DeleteProductFromGoogleWhenExcludedJob;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->store_id;
        $products = Product::where('user_store_id', $storeId);
        return PrimevueDatatables::of($products)->make();
    }

    public function home(Request $request)
    {
        $storeId = $request->storeId;
        $lastSync = SyncDetails::select('last_sync')->where('id', '=', md5($storeId . 'products'))->first();
        $productsData = Product::where('store_id', $storeId)->simplePaginate(10);
        if ($lastSync != null) {
            $lastSyncDate = gmdate('Y-m-d @ H:i:s e', $lastSync->last_sync);
            return ApiResponser::success(['lastSyncDate' =>   $lastSyncDate]);
        }
        return ApiResponser::success(['productsData' =>   $productsData]);
    }

    public function syncProducts(Request $request)
    {
        $storeId = $request->storeId;
        dispatch(new SendProductsToGoogleJob($storeId));
        return ApiResponser::success(['success' => 'Hello!, Your Products is being uploaded']);
    }

    public function deleteProducts(Request $request)
    {
        $storeId = $request->store_id;
        dispatch(new deleteDeletedProductsFromGoogleJob($storeId));
        return ApiResponser::success(['success' => 'Hello!, Your Deleted Products is being Deleted']);
    }

    public function logout(Request $request)
    {
        $storeId = $request->store_id;
        try {
            $thirdParty = UserSetting::where('user_store_id', '=', $storeId)->delete();
            UserSetting::where('user_store_id', '=', $storeId)->delete();
            Product::where('user_store_id', '=', $storeId)->delete();
            Log::where('user_store_id', '=', $storeId)->delete();
            SyncDetails::where('sync_store_id', '=', $storeId)->delete();
            if ($thirdParty) {
                return ApiResponser::success(['message' => 'user data deleted successfully']);
            }
            return $this->error('error deleting the data from the database', 500);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    public function stat(Request $request)
    {
        $currentDate = Carbon::now();
        $days = $request->input('days');
        if (empty($days) || $days == 'null') {
            $startDate = Carbon::create(1970, 1, 1);
        } else {
            $startDate = $currentDate->subDays($days);
        }
        $endDate = Carbon::now();
        $storeId = $request->store_id;
        $all = Product::where('user_store_id', $storeId)->whereBetween('updated_at', [$startDate, $endDate])->count();
        $submittedCount = Product::query()->where('user_store_id', $storeId)->whereBetween('updated_at', [$startDate, $endDate])->where('status', 'success')->count();
        $withErrorCount = Product::query()->where('user_store_id', $storeId)->whereBetween('updated_at', [$startDate, $endDate])->Where('status', 'error')->count();
        $excluded = Product::query()->where('user_store_id', $storeId)->whereBetween('updated_at', [$startDate, $endDate])->where('is_excluded', true)->count();
        $output = ['all' =>   ['count' => $all], 'submitted' => ['count' => $submittedCount], 'with error' => ['count' => $withErrorCount], 'excluded' => ['count' => $excluded]];
        return response()->json($output);
    }

    public function uninstall(Request $request)
    {
        $decoded = JWT::decode($request->session_token, new Key(env('APP_SECRET'), ('HS256')));
        $payload = json_decode(json_encode($decoded), true);
        $storeId = (int)$payload['store_id'];
        Oauth::where('user_store_id', '=', $storeId)->delete();
        State::where('user_store_id', '=', $storeId)->delete();
        // Log::where('user_store_id', '=', $storeId)->delete();
        SyncDetails::where('sync_store_id', '=', $storeId)->delete();
        // GoogleSetting::where('user_store_id', '=', $storeId)->delete();
        // User:where('store_id',storeId)->delete();
        $this->removeClientIdFromProxyRequest($storeId);
    }

    private function removeClientIdFromProxyRequest($storeId)
    {
        $client = new Client();
        $clientId = env("APP_CLIENT_ID");
        try {
            $curlGetAccessTokenRequest = new GuzzleHttp('GET', "http://localhost:3333/clear?client_id=$clientId&store_id=$storeId");
            $client->sendAsync($curlGetAccessTokenRequest)->wait();
        } catch (\Throwable $th) {
            ErrorLogger::logError($th, $storeId);
        }
    }

    // public function excludeStatus(Request $request)
    // {
    //     $storeId = $request->store_id;
    //     $request->validate(['is_excluded' => 'required|boolean', 'product_id' => 'required', 'variant_id' => 'required']);
    //     try {
    //         if ($request->is_excluded) {
    //             dispatch(new DeleteProductFromGoogleWhenExcludedJob($request->user(), $request->product_id, $request->variant_id));
    //         } elseif (!$request->is_excluded) {
    //             dispatch(new SendProductToGoogleBackJob($request->user(), $request->product_id, $request->variant_id));
    //         }
    //         Product::query()->where('user_store_id', $storeId)->where('product_id', $request->product_id)->where('variant_id', $request->variant_id)->update(['is_excluded' => $request->is_excluded]);
    //         return response()->json(['message' => 'success', 'status' => true]);
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => 'fail',  'status' => false, 'message' => $th->getMessage()]);
    //     }
    // }
}
