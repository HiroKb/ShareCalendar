<?php

namespace App\Http\Middleware\Admin;

use App\Models\AdminLogin;
use Closure;

class RecordIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (AdminLogin::count() >= 20){
            AdminLogin::oldest()->first()->delete();
        }

        $adminLogin = new AdminLogin();
        $adminLogin->ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $adminLogin->save();
        
        return $next($request);
    }
}
