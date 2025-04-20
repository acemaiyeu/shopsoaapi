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
        if ($request->route()->uri() === "api/v1/profile" || $request->route()->uri() === "api/v1/my-orders" || $request->route()->uri() === "api/v1/change-password"){
            return $next($request);
        }   
        if (auth()->user()->role->code == "SUPERADMIN" || auth()->user()->role->code == "ADMIN"){
            return $next($request);
        }else{
            return response(["message" => "Bạn không có quyền truy cập api này"], 400);
        }
    }
}