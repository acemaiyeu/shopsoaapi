<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permissionCode)
    {
        $user = Auth::user(); // Lấy user hiện tại

        // Kiểm tra nếu user không có quyền thì trả về lỗi 403
        if ((!$user || !$user->hasPermission($permissionCode)) && auth()->user()->role->code != 'SUPERADMIN') {
            return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
        }

        return $next($request);
    }
}
