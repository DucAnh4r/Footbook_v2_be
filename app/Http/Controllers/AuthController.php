<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
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

        // Tạo access token
        $accessToken = Str::random(80);

        // Tạo user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password_hash' => $request->password, // Sẽ tự động hash qua Mutator
            'birth_year' => $request->birth_year,
            'profession' => $request->profession,
            'auth_provider' => $request->auth_provider,
            'avatar_url' => $request->avatar_url,
            'cover_photo_url' => $request->cover_photo_url,
            'address' => $request->address,
            'status' => $request->status,
            'access_token' => $accessToken,
            'token_expires_at' => now()->addDays(30) // Token hết hạn sau 30 ngày
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->makeHidden(['access_token']), // Ẩn token trong response user
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60 // 30 ngày tính bằng giây
        ], 201);
    }

    public function login(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm user theo email
        $user = User::where('email', $request->email)->first();

        // Kiểm tra user tồn tại và mật khẩu đúng
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // Tạo access token mới
        $accessToken = Str::random(80);
        
        // Cập nhật token cho user
        $user->update([
            'access_token' => $accessToken,
            'token_expires_at' => now()->addDays(30)
        ]);

        // Đăng nhập thành công
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user->makeHidden(['access_token']), // Ẩn token trong response user
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60 // 30 ngày tính bằng giây
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        if ($user) {
            // Xóa access token
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
        $user = $request->user();
        
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

        return response()->json([
            'message' => 'Token không hợp lệ'
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