<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateAvatar(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'avatar_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm user theo ID
        $user = User::find($request->user_id);

        // Cập nhật ảnh đại diện
        $user->avatar_url = $request->avatar_url;
        $user->save();

        return response()->json([
            'message' => 'Cập nhật ảnh đại diện thành công',
            'user' => $user
        ]);
    }
}
