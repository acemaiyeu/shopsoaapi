<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        if (empty(auth()->user())){
            return response(["message" => "Bạn không có quyền truy cập api này"], 400);
        }
        if (auth()->user()->role_code == "SUPERADMIN" || auth()->user()->role_code == "ADMIN"){
            return $next($request);
        }else{
            return response(["message" => "Bạn không có quyền truy cập api này"], 400);
        }
        
    }
}
