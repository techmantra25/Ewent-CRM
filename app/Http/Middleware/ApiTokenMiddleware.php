<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiToken;

class ApiTokenMiddleware
{
      public function handle(Request $request, Closure $next)
    {
        $plainToken = $request->header('API-TOKEN');

        if (!$plainToken) {
            return response()->json(['message' => 'Token missing'], 401);
        }

        $apiToken = ApiToken::where('token', $plainToken)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$apiToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

}
