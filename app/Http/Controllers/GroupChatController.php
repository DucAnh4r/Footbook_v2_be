<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupChat;
use App\Models\GroupMessage;
use App\Models\GroupChatImage;
use App\Models\GroupChatMember;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class GroupChatController extends Controller
{
    // Kiểm tra thành viên của nhóm chat
    private function checkGroupMembership($group_id, $user_id)
    {
        return GroupChatMember::where('group_chat_id', $group_id)
            ->where('user_id', $user_id)
            ->exists();
    }

    // Tạo nhóm chat mới
    public function createGroupChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'creator_id' => 'required|exists:users,id',
            'avatar_url' => 'nullable|string|max:500',
            'members' => 'required|array|min:3',
            'members.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tạo nhóm chat
        $groupChat = GroupChat::create([
            'name' => $request->name,
            'avatar_url' => $request->avatar_url
        ]);

        // Thêm người tạo và các thành viên vào nhóm
        $members = array_unique(array_merge([$request->creator_id], $request->members));
        
        foreach ($members as $member_id) {
            GroupChatMember::create([
                'group_chat_id' => $groupChat->id,
                'user_id' => $member_id,
                'joined_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Group chat created successfully',
            'group' => $groupChat
        ], 201);
    }

    // Gửi tin nhắn nhóm (văn bản hoặc ảnh)
    public function sendGroupMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_chat_id' => 'required|exists:group_chat,id',
            'sender_id' => 'required|exists:users,id',
            'content' => 'required_without:image_url|string|nullable',
            'type' => 'required|in:text,image',
            'image_url' => 'required_if:type,image|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem người gửi có phải là thành viên của nhóm không
        if (!$this->checkGroupMembership($request->group_chat_id, $request->sender_id)) {
            return response()->json(['error' => 'User is not a member of this group chat'], 403);
        }

        // Tạo tin nhắn
        $message = GroupMessage::create([
            'group_chat_id' => $request->group_chat_id,
            'sender_id' => $request->sender_id,
            'content' => $request->content ?? '',
            'type' => $request->type
        ]);

        // Nếu là tin nhắn ảnh, lưu URL ảnh vào bảng group_chat_images
        if ($request->type == 'image' && $request->has('image_url')) {
            GroupChatImage::create([
                'group_message_id' => $message->id,
                'image_url' => $request->image_url
            ]);
        }

        // Load thông tin ảnh nếu là tin nhắn ảnh
        if ($message->type == 'image') {
            $message->image = $message->groupChatImage;
        }

        return response()->json(['message' => $message], 201);
    }

    // Lấy tin nhắn của một nhóm chat
    public function getGroupMessages($group_id)
    {
        $messages = GroupMessage::where('group_chat_id', $group_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Load dữ liệu ảnh cho tin nhắn ảnh và thông tin người gửi
        foreach ($messages as $message) {
            $message->sender = User::find($message->sender_id);
            
            if ($message->type == 'image') {
                $message->image = GroupChatImage::where('group_message_id', $message->id)->first();
            }
        }

        return response()->json(['messages' => $messages]);
    }

    // Lấy danh sách các nhóm chat của một user
    public function getUserGroupChats($user_id)
    {
        $groupMemberships = GroupChatMember::where('user_id', $user_id)->get();
        $groupChats = [];

        foreach ($groupMemberships as $membership) {
            $groupChat = GroupChat::find($membership->group_chat_id);
            
            // Lấy tin nhắn cuối cùng của nhóm
            $lastMessage = GroupMessage::where('group_chat_id', $groupChat->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastMessage) {
                // Thêm thông tin người gửi tin nhắn cuối
                $lastMessage->sender = User::find($lastMessage->sender_id);
                
                if ($lastMessage->type == 'image') {
                    $lastMessage->image = GroupChatImage::where('group_message_id', $lastMessage->id)->first();
                }
                
                $groupChat->last_message = $lastMessage;
            }

            // Đếm số thành viên trong nhóm
            $groupChat->member_count = GroupChatMember::where('group_chat_id', $groupChat->id)->count();
            
            $groupChats[] = $groupChat;
        }

        return response()->json(['group_chats' => $groupChats]);
    }

    // Thêm thành viên vào nhóm chat
    public function addGroupMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_chat_id' => 'required|exists:group_chat,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra xem người được thêm đã là thành viên chưa
        if ($this->checkGroupMembership($request->group_chat_id, $request->user_id)) {
            return response()->json(['error' => 'User is already a member of this group chat'], 422);
        }

        // Thêm thành viên mới
        $groupMember = GroupChatMember::create([
            'group_chat_id' => $request->group_chat_id,
            'user_id' => $request->user_id,
            'joined_at' => now()
        ]);

        return response()->json([
            'message' => 'User added to group chat successfully',
            'member' => $groupMember
        ], 201);
    }

    // Xóa thành viên khỏi nhóm chat
    public function removeGroupMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_chat_id' => 'required|exists:group_chat,id',
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Xóa thành viên
        $deleted = GroupChatMember::where('group_chat_id', $request->group_chat_id)
            ->where('user_id', $request->user_id)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'User is not a member of this group chat'], 404);
        }

        return response()->json(['message' => 'User removed from group chat successfully']);
    }

    // Lấy danh sách thành viên của nhóm chat
    public function getGroupMembers($group_id)
    {
        $members = GroupChatMember::where('group_chat_id', $group_id)->get();
        $memberDetails = [];

        foreach ($members as $member) {
            $user = User::find($member->user_id);
            $user->joined_at = $member->joined_at;
            $memberDetails[] = $user;
        }

        return response()->json(['members' => $memberDetails]);
    }

    // Cập nhật thông tin nhóm chat
    public function updateGroupChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_chat_id' => 'required|exists:group_chat,id',
            'name' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $groupChat = GroupChat::find($request->group_chat_id);
        
        if ($request->has('name')) {
            $groupChat->name = $request->name;
        }
        
        if ($request->has('avatar_url')) {
            $groupChat->avatar_url = $request->avatar_url;
        }
        
        $groupChat->save();

        return response()->json([
            'message' => 'Group chat updated successfully',
            'group' => $groupChat
        ]);
    }
}