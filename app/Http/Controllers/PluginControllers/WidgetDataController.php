<?php

namespace App\Http\Controllers\PluginControllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WidgetDataController extends Controller
{
    public function data(Request $request)
    {
        $request->validate(['product_id' => 'required',  'variant_id' => 'required',]);
        try {
            $data = Product::where('user_Store_id', $request->user()->store_id)->where('product_id', $request->product_id)->where('variant_id', $request->variant_id)->first();
        } catch (\Throwable $th) {
            return response()->json(['status' => 'fail',  'message' => $th->getMessage(), 'data' => []]);
        }
        if (!empty($data)) {
            $data =   ['errors' => $data->google_error_array ?? [], 'status' => $data->status ?? 'error', 'updated_at' => $data->updated_at];
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        return response()->json(['status' => 'fail', 'data' => []]);
    }
}
