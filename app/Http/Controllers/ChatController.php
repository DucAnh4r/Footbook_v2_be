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

    // Lấy danh sách cuộc trò chuyện của một user
    public function getUserConversations($user_id)
    {
        $conversations = Conversation::where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Load tin nhắn cuối cùng cho mỗi cuộc trò chuyện
        foreach ($conversations as $conversation) {
            $lastMessage = PrivateMessage::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastMessage && $lastMessage->type == 'image') {
                $lastMessage->image = ChatImage::where('message_id', $lastMessage->id)->first();
            }

            $conversation->last_message = $lastMessage;

            // Lấy thông tin người dùng khác
            if ($conversation->user1_id == $user_id) {
                $conversation->other_user = User::find($conversation->user2_id);
            } else {
                $conversation->other_user = User::find($conversation->user1_id);
            }
        }

        return response()->json(['conversations' => $conversations]);
    }
}
