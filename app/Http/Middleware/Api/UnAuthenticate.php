<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Support\Facades\Auth;

class UnAuthenticate
{
    /**
     * 認証済みのアクセスは420コードを返す
     * @param $request
     * @param Closure $next
     * @return Closure|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return response([], 420);
        }
        return $next($request);
    }
}
