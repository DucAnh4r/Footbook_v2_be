<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    /**
     * Create a new group
     */
    public function createGroup(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'avatar_url' => 'nullable|url',
            'user_id' => 'required|exists:users,id', // Creator user ID
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new group
        $group = Group::create([
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'created_at' => now(),
        ]);

        // Add creator as a member
        DB::table('group_members')->insert([
            'group_id' => $group->id,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'message' => 'Đã tạo nhóm thành công',
            'group' => $group
        ], 201);
    }

    /**
     * Get group details
     */
    public function getGroup($id)
    {
        $group = Group::with('members')->find($id);

        if (!$group) {
            return response()->json([
                'message' => 'Không tìm thấy nhóm'
            ], 404);
        }

        // Get member count
        $memberCount = $group->members()->count();

        // Get post count
        $postCount = $group->posts()->count();

        return response()->json([
            'group' => $group,
            'member_count' => $memberCount,
            'post_count' => $postCount
        ]);
    }

    /**
     * Update group information
     */
    public function updateGroup(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id', // User making the update
            'name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is a member of the group
        $isMember = DB::table('group_members')
            ->where('group_id', $request->group_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Find group
        $group = Group::find($request->group_id);

        // Update group info
        if ($request->has('name')) {
            $group->name = $request->name;
        }

        if ($request->has('avatar_url')) {
            $group->avatar_url = $request->avatar_url;
        }

        $group->save();

        return response()->json([
            'message' => 'Đã cập nhật thông tin nhóm thành công',
            'group' => $group
        ]);
    }

    /**
     * Delete a group
     */
    public function deleteGroup(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id', // User attempting to delete
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is a member of the group
        $isMember = DB::table('group_members')
            ->where('group_id', $request->group_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if (!$isMember) {
            return response()->json([
                'message' => 'Bạn không phải là thành viên của nhóm này'
            ], 403);
        }

        // Find group
        $group = Group::find($request->group_id);

        // Delete group (this will cascade to group_members through DB constraints)
        $group->delete();

        return response()->json([
            'message' => 'Đã xóa nhóm thành công'
        ]);
    }

    /**
     * Add a user to a group
     */
    public function addMember(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id', // User to add
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is already a member
        $isAlreadyMember = DB::table('group_members')
            ->where('group_id', $request->group_id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($isAlreadyMember) {
            return response()->json([
                'message' => 'Người dùng đã là thành viên của nhóm này'
            ], 409);
        }

        // Add user to group
        DB::table('group_members')->insert([
            'group_id' => $request->group_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'message' => 'Đã thêm thành viên vào nhóm thành công'
        ], 201);
    }

    /**
     * Remove a user from a group
     */
    public function removeMember(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id', // User to remove
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Remove user from group
        $removed = DB::table('group_members')
            ->where('group_id', $request->group_id)
            ->where('user_id', $request->user_id)
            ->delete();

        if ($removed) {
            return response()->json([
                'message' => 'Đã xóa thành viên khỏi nhóm thành công'
            ]);
        }

        return response()->json([
            'message' => 'Không tìm thấy thành viên trong nhóm'
        ], 404);
    }

    /**
     * Get group members
     */
    public function getGroupMembers(Request $request, $group_id)
    {
        // Kiểm tra group có tồn tại không
        $group = Group::find($group_id);
        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Lấy limit và offset từ query string (mặc định limit = 20, offset = 0)
        $limit = $request->query('limit', 20);
        $offset = $request->query('offset', 0);

        // Validate limit và offset
        if (!is_numeric($limit) || $limit < 1 || !is_numeric($offset) || $offset < 0) {
            return response()->json(['error' => 'Invalid limit or offset'], 422);
        }

        // Lấy danh sách thành viên của group (có phân trang)
        $members = $group->members()
            ->select('users.id', 'users.name', 'users.avatar_url')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Tổng số thành viên
        $totalCount = $group->members()->count();

        return response()->json([
            'members' => $members,
            'total_count' => $totalCount
        ]);
    }


    /**
     * Get posts from a group
     */
    public function getGroupPosts(Request $request, $group_id, $user_id = null)
    {
        // Kiểm tra nhóm có tồn tại không
        if (!Group::where('id', $group_id)->exists()) {
            return response()->json(['message' => 'Nhóm không tồn tại'], 404);
        }

        // Nếu có user_id, kiểm tra user có tồn tại và có phải thành viên không
        if ($user_id) {
            if (!User::where('id', $user_id)->exists()) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            if (!DB::table('group_members')
                ->where('group_id', $group_id)
                ->where('user_id', $user_id)
                ->exists()) {
                return response()->json(['message' => 'Bạn không phải là thành viên của nhóm này'], 403);
            }
        }

        // Lấy dữ liệu phân trang từ query string
        $limit = $request->query('limit', 10);  // Mặc định là 10
        $offset = $request->query('offset', 0); // Mặc định là 0

        // Validate limit và offset
        if (!is_numeric($limit) || $limit < 1 || !is_numeric($offset) || $offset < 0) {
            return response()->json(['error' => 'Invalid limit or offset'], 422);
        }

        // Lấy danh sách bài viết của nhóm
        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where('group_id', $group_id)
            ->latest()
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json(['posts' => $posts]);
    }


    /**
     * Get all groups a user is a member of
     */
    public function getUserGroups(Request $request, $user_id)
    {
        // Kiểm tra user có tồn tại không
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }

        // Lấy limit & offset từ query params, mặc định: limit = 10, offset = 0
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 0);

        // Validate limit và offset
        if (!is_numeric($limit) || $limit < 1 || !is_numeric($offset) || $offset < 0) {
            return response()->json(['error' => 'Invalid limit or offset'], 422);
        }

        // Lấy danh sách nhóm có phân trang
        $groups = $user->groups()->limit($limit)->offset($offset)->get();

        // Lấy tổng số nhóm
        $totalCount = $user->groups()->count();

        return response()->json([
            'groups' => $groups,
            'total_count' => $totalCount
        ]);
    }


    /**
     * Search for groups
     */
    public function searchGroups(Request $request, $query)
    {
        // Lấy limit & offset từ query params, mặc định: limit = 10, offset = 0
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 0);

        // Validate limit và offset
        if (!is_numeric($limit) || $limit < 1 || !is_numeric($offset) || $offset < 0) {
            return response()->json(['error' => 'Invalid limit or offset'], 422);
        }

        // Search for groups
        $groups = Group::where('name', 'like', '%' . $query . '%')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Get total count
        $totalCount = Group::where('name', 'like', '%' . $query . '%')->count();

        return response()->json([
            'groups' => $groups,
            'total_count' => $totalCount
        ]);
    }
}
