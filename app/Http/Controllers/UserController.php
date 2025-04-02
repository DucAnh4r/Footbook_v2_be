<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'avatar_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        $user->avatar_url = $request->avatar_url;
        $user->save();

        return response()->json([
            'message' => 'Cập nhật ảnh đại diện thành công',
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'birth_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'profession' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy user theo ID
        $user = User::find($request->user_id);

        // Cập nhật thông tin nếu có dữ liệu đầu vào
        if ($request->has('birth_year')) {
            $user->birth_year = $request->birth_year;
        }
        if ($request->has('profession')) {
            $user->profession = $request->profession;
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }

        // Lưu thay đổi
        $user->save();

        return response()->json([
            'message' => 'Cập nhật thông tin tài khoản thành công',
            'user' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // Yêu cầu nhập lại mật khẩu mới
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm user
        $user = User::find($request->user_id);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json(['error' => 'Mật khẩu hiện tại không đúng'], 401);
        }

        // Cập nhật mật khẩu mới
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }

    public function getProfile($id)
    {
        // Tìm user theo ID
        $user = User::select([
            'id',
            'name',
            'birth_year',
            'profession',
            'avatar_url',
            'cover_photo_url',
            'address'
        ])->find($id);

        // Kiểm tra user có tồn tại không
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại'], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin thành công',
            'user' => $user
        ]);
    }
}
