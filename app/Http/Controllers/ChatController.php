<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\ChatImage;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    // Kiểm tra hoặc tạo cuộc trò chuyện giữa 2 user
    private function getOrCreateConversation($user1_id, $user2_id)
    {
        $conversation = Conversation::where(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user1_id)->where('user2_id', $user2_id);
        })->orWhere(function ($query) use ($user1_id, $user2_id) {
            $query->where('user1_id', $user2_id)->where('user2_id', $user1_id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user1_id,
                'user2_id' => $user2_id
            ]);
        }

        return $conversation;
    }

    public function getConversationBetweenUsers($user1_id, $user2_id)
    {
        // Kiểm tra người dùng tồn tại
        $user1 = User::find($user1_id);
        $user2 = User::find($user2_id);

        if (!$user1 || !$user2) {
            return response()->json(['error' => 'Không tìm thấy người dùng'], 404);
        }

        // Sử dụng hàm đã có để tìm cuộc trò chuyện
        $conversation = $this->getOrCreateConversation($user1_id, $user2_id);

        // Lấy tin nhắn của cuộc trò chuyện
        $messages = PrivateMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Load dữ liệu ảnh cho tin nhắn ảnh
        foreach ($messages as $message) {
            if ($message->type == 'image') {
                $message->image = ChatImage::where('message_id', $message->id)->first();
            }
        }

        // Thêm thông tin người dùng
        $userData = [
            'user1' => $user1,
            'user2' => $user2
        ];

        return response()->json([
            'conversation' => $conversation,
            'users' => $userData,
            'messages' => $messages
        ]);
    }

    // Gửi tin nhắn văn bản hoặc ảnh
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required_without:image_url|string|nullable',
            'type' => 'required|in:text,image',
            'image_url' => 'required_if:type,image|url' // Chỉ yêu cầu URL ảnh khi type là image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = $this->getOrCreateConversation($request->sender_id, $request->receiver_id);

        // Tạo tin nhắn
        $message = PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content ?? '', // Nội dung rỗng đối với tin nhắn ảnh
            'type' => $request->type
        ]);

        // Nếu là tin nhắn ảnh, lưu URL ảnh vào bảng chat_images
        if ($request->type == 'image' && $request->has('image_url')) {
            // Lưu URL ảnh vào bảng chat_images, tham chiếu tới bảng private_messages
            ChatImage::create([
                'message_id' => $message->id, // Đảm bảo ID của bảng private_messages
                'image_url' => $request->image_url
            ]);
        }

        // Load thông tin ảnh nếu là tin nhắn ảnh
        if ($message->type == 'image') {
            $message->image = $message->chatImage;
        }

        return response()->json(['message' => $message], 201);
    }


    // Lấy tin nhắn của một cuộc trò chuyện
    public function getMessages($conversation_id)
    {
        $messages = PrivateMessage::where('conversation_id', $conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Load dữ liệu ảnh cho tin nhắn ảnh
        foreach ($messages as $message) {
            if ($message->type == 'image') {
                $message->image = ChatImage::where('message_id', $message->id)->first();
            }
        }

        return response()->json(['messages' => $messages]);
    }

    public function getUserConversations($user_id)
    {
        $conversations = Conversation::where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->get();

        // Gắn last_message và other_user
        foreach ($conversations as $conversation) {
            $lastMessage = PrivateMessage::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastMessage && $lastMessage->type == 'image') {
                $lastMessage->image = ChatImage::where('message_id', $lastMessage->id)->first();
            }

            $conversation->last_message = $lastMessage;

            // Lấy người đối thoại
            $conversation->other_user = ($conversation->user1_id == $user_id)
                ? User::find($conversation->user2_id)
                : User::find($conversation->user1_id);
        }

        // ✅ Sắp xếp lại theo thời gian tin nhắn mới nhất
        $sorted = $conversations->sortByDesc(function ($conv) {
            return optional($conv->last_message)->created_at;
        })->values(); // reset chỉ số

        return response()->json(['conversations' => $sorted]);
    }
}
