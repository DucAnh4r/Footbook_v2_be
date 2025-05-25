<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'birth_year' => 'nullable|integer',
            'profession' => 'nullable|string',
            'auth_provider' => 'required|in:google,facebook,local',
            'avatar_url' => 'nullable|url',
            'cover_photo_url' => 'nullable|url',
            'address' => 'nullable|string',
            'status' => 'required|in:available,unavailable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $accessToken = Str::random(80);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => $request->password,
            'birth_year' => $request->birth_year,
            'profession' => $request->profession,
            'auth_provider' => $request->auth_provider,
            'avatar_url' => $request->avatar_url,
            'cover_photo_url' => $request->cover_photo_url,
            'address' => $request->address,
            'status' => $request->status,
            'access_token' => $accessToken,
            'token_expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->makeHidden(['access_token']),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $accessToken = Str::random(80);
        
        $user->update([
            'access_token' => $accessToken,
            'token_expires_at' => now()->addDays(30)
        ]);

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user->makeHidden(['access_token']),
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            $user->update([
                'access_token' => null,
                'token_expires_at' => null
            ]);

            return response()->json([
                'message' => 'Đăng xuất thành công'
            ]);
        }

        return response()->json([
            'message' => 'Token không hợp lệ'
        ], 401);
    }

    public function refreshToken(Request $request)
    {
        // Lấy token từ header Authorization
        $authHeader = $request->header('Authorization');
        Log::info('Refresh token request', ['authHeader' => $authHeader]);
        
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Log::info('Token not provided');
            return response()->json([
                'message' => 'Token không được cung cấp',
                'error_code' => 'MISSING_TOKEN'
            ], 401);
        }

        // Lấy token (bỏ phần "Bearer ")
        $token = substr($authHeader, 7);
        Log::info('Token extracted', ['token' => $token]);

        // Tìm user với token này
        $user = User::where('access_token', $token)->first();
        Log::info('User lookup', ['user_found' => !is_null($user), 'token' => $token]);

        if ($user) {
            // Tạo token mới
            $accessToken = Str::random(80);
            
            $user->update([
                'access_token' => $accessToken,
                'token_expires_at' => now()->addDays(30)
            ]);

            return response()->json([
                'message' => 'Token đã được làm mới',
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => 30 * 24 * 60 * 60
            ]);
        }

        Log::info('Token invalid', ['token' => $token]);
        return response()->json([
            'message' => 'Token không hợp lệ',
            'error_code' => 'INVALID_TOKEN'
        ], 401);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            return response()->json([
                'message' => 'Thông tin người dùng hiện tại',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Token không hợp lệ'
        ], 401);
    }
}