<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
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

        // Đăng nhập thành công
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user
        ]);
    }
}
