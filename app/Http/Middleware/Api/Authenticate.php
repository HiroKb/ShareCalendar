<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * 未認証のアクセスは401コードを返す
     * @param $request
     * @param Closure $next
     * @return Closure|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return response([], 401);
        }
        return $next($request);
    }
}
