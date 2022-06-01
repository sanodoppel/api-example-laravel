<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;

class RefreshToken
{
    public function handle($request, Closure $next)
    {
        if (!$request->bearerToken()) {
            AuthService::throwNotAuthExeption();
        }

        return $next($request);
    }
}
