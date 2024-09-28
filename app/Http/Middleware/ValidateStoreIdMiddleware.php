<?php

namespace App\Http\Middleware;

use Closure;

class ValidateStoreIdMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->has('store_id') && isset($request->user()->store_id)) {

            $request->merge(['store_id' => $request->user()->store_id]);
        }

        $request->validate([
            'store_id' => 'required|exists:users,store_id',
        ]);

        return $next($request);
    }
}
