<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy token từ header Authorization
        $authHeader = $request->header('Authorization');
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Thiếu header Authorization hoặc định dạng không đúng',
                'error_code' => 'MISSING_TOKEN'
            ], 401);
        }

        // Lấy token (bỏ phần "Bearer ")
        $token = substr($authHeader, 7);

        // Tìm user với token này
        $user = User::where('access_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token không hợp lệ',
                'error_code' => 'INVALID_TOKEN'
            ], 401);
        }

        // Kiểm tra token có hết hạn không
        if (!$user->isTokenValid()) {
            return response()->json([
                'message' => 'Token đã hết hạn',
                'error_code' => 'EXPIRED_TOKEN'
            ], 401);
        }

        // Log để debug
        Log::info('User authenticated', ['user_id' => $user->id, 'ip' => $request->ip()]);

        // Gán user vào request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}