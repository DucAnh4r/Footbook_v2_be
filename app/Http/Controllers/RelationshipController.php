<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RelationshipController extends Controller
{
    /**
     * Gửi lời mời kết bạn
     */
    public function sendFriendRequest(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'requester_id' => 'required|exists:users,id',
            'addressee_id' => 'required|exists:users,id|different:requester_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem đã có mối quan hệ chưa
        $existingRelationship = Relationship::where(function ($query) use ($request) {
            $query->where('requester_id', $request->requester_id)
                ->where('addressee_id', $request->addressee_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('requester_id', $request->addressee_id)
                ->where('addressee_id', $request->requester_id);
        })->first();

        if ($existingRelationship) {
            return response()->json([
                'message' => 'Đã tồn tại mối quan hệ giữa hai người dùng này',
                'relationship' => $existingRelationship
            ], 400);
        }

        // Tạo mối quan hệ mới
        $relationship = Relationship::create([
            'requester_id' => $request->requester_id,
            'addressee_id' => $request->addressee_id,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Đã gửi lời mời kết bạn thành công',
            'relationship' => $relationship
        ], 201);
    }

    /**
     * Chấp nhận lời mời kết bạn
     */
    public function acceptFriendRequest(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'requester_id' => 'required|exists:users,id',
            'addressee_id' => 'required|exists:users,id|different:requester_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm mối quan hệ
        $relationship = Relationship::where('requester_id', $request->requester_id)
            ->where('addressee_id', $request->addressee_id)
            ->where('status', 'pending')
            ->first();

        if (!$relationship) {
            return response()->json([
                'message' => 'Không tìm thấy lời mời kết bạn'
            ], 404);
        }

        // Cập nhật trạng thái
        $relationship->status = 'accepted';
        $relationship->save();

        return response()->json([
            'message' => 'Đã chấp nhận lời mời kết bạn',
            'relationship' => $relationship
        ]);
    }

    /**
     * Từ chối/Xóa lời mời kết bạn
     */
    public function declineFriendRequest(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'requester_id' => 'required|exists:users,id',
            'addressee_id' => 'required|exists:users,id|different:requester_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm mối quan hệ
        $relationship = Relationship::where('requester_id', $request->requester_id)
            ->where('addressee_id', $request->addressee_id)
            ->where('status', 'pending')
            ->first();

        if (!$relationship) {
            return response()->json([
                'message' => 'Không tìm thấy lời mời kết bạn'
            ], 404);
        }

        // Xóa mối quan hệ
        $relationship->delete();

        return response()->json([
            'message' => 'Đã từ chối lời mời kết bạn'
        ]);
    }

    /**
     * Chặn người dùng
     */
    public function blockUser(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blocked_user_id' => 'required|exists:users,id|different:user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem đã có mối quan hệ chưa
        $existingRelationship = Relationship::where(function ($query) use ($request) {
            $query->where('requester_id', $request->user_id)
                ->where('addressee_id', $request->blocked_user_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('requester_id', $request->blocked_user_id)
                ->where('addressee_id', $request->user_id);
        })->first();

        if ($existingRelationship) {
            // Cập nhật thành trạng thái blocked
            $existingRelationship->requester_id = $request->user_id;
            $existingRelationship->addressee_id = $request->blocked_user_id;
            $existingRelationship->status = 'blocked';
            $existingRelationship->save();
        } else {
            // Tạo mối quan hệ mới với trạng thái blocked
            $existingRelationship = Relationship::create([
                'requester_id' => $request->user_id,
                'addressee_id' => $request->blocked_user_id,
                'status' => 'blocked'
            ]);
        }

        return response()->json([
            'message' => 'Đã chặn người dùng thành công',
            'relationship' => $existingRelationship
        ]);
    }

    /**
     * Bỏ chặn người dùng
     */
    public function unblockUser(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'blocked_user_id' => 'required|exists:users,id|different:user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm mối quan hệ
        $relationship = Relationship::where('requester_id', $request->user_id)
            ->where('addressee_id', $request->blocked_user_id)
            ->where('status', 'blocked')
            ->first();

        if (!$relationship) {
            return response()->json([
                'message' => 'Không tìm thấy mối quan hệ chặn'
            ], 404);
        }

        // Xóa mối quan hệ
        $relationship->delete();

        return response()->json([
            'message' => 'Đã bỏ chặn người dùng'
        ]);
    }

    /**
     * Hủy kết bạn
     */
    public function unfriend(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'friend_id' => 'required|exists:users,id|different:user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm mối quan hệ
        $relationship = Relationship::where(function ($query) use ($request) {
            $query->where('requester_id', $request->user_id)
                ->where('addressee_id', $request->friend_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('requester_id', $request->friend_id)
                ->where('addressee_id', $request->user_id);
        })->where('status', 'accepted')
            ->first();

        if (!$relationship) {
            return response()->json([
                'message' => 'Không tìm thấy mối quan hệ bạn bè'
            ], 404);
        }

        // Xóa mối quan hệ
        $relationship->delete();

        return response()->json([
            'message' => 'Đã hủy kết bạn thành công'
        ]);
    }

    /**
     * Lấy danh sách lời mời kết bạn đã gửi
     */
    public function getSentFriendRequests(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy danh sách lời mời
        $sentRequests = Relationship::with('addressee')
            ->where('requester_id', $request->user_id)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'sent_requests' => $sentRequests
        ]);
    }

    /**
     * Lấy danh sách lời mời kết bạn đã nhận
     */
    public function getReceivedFriendRequests(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy danh sách lời mời
        $receivedRequests = Relationship::with('requester')
            ->where('addressee_id', $request->user_id)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'received_requests' => $receivedRequests
        ]);
    }

    /**
     * Lấy danh sách bạn bè
     */
    public function getFriends(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy danh sách bạn bè từ cả hai chiều của mối quan hệ
        $friendsAsRequester = Relationship::with('addressee')
            ->where('requester_id', $request->user_id)
            ->where('status', 'accepted')
            ->get()
            ->pluck('addressee');

        $friendsAsAddressee = Relationship::with('requester')
            ->where('addressee_id', $request->user_id)
            ->where('status', 'accepted')
            ->get()
            ->pluck('requester');

        // Kết hợp cả hai danh sách
        $friends = $friendsAsRequester->merge($friendsAsAddressee);

        return response()->json([
            'friends' => $friends
        ]);
    }

    /**
     * Đếm số lượng bạn bè của người dùng
     */
    public function countFriends(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Đếm bạn bè từ cả hai chiều của mối quan hệ
        $countAsRequester = Relationship::where('requester_id', $request->user_id)
            ->where('status', 'accepted')
            ->count();

        $countAsAddressee = Relationship::where('addressee_id', $request->user_id)
            ->where('status', 'accepted')
            ->count();

        // Tổng số bạn bè
        $totalFriends = $countAsRequester + $countAsAddressee;

        return response()->json([
            'user_id' => $request->user_id,
            'friends_count' => $totalFriends
        ]);
    }

    /**
     * Kiểm tra trạng thái mối quan hệ giữa hai người dùng
     */
    public function checkRelationshipStatus(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'other_user_id' => 'required|exists:users,id|different:user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm mối quan hệ
        $relationship = Relationship::where(function ($query) use ($request) {
            $query->where('requester_id', $request->user_id)
                ->where('addressee_id', $request->other_user_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('requester_id', $request->other_user_id)
                ->where('addressee_id', $request->user_id);
        })->first();

        if (!$relationship) {
            return response()->json([
                'status' => 'strangers'
            ]);
        }

        // Xác định vai trò của user hiện tại trong mối quan hệ
        $userRole = null;
        if ($relationship->requester_id == $request->user_id) {
            $userRole = 'requester';
        } else {
            $userRole = 'addressee';
        }

        return response()->json([
            'relationship' => $relationship,
            'user_role' => $userRole
        ]);
    }
}
