<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\PrivateMessage;
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

    // Gửi tin nhắn
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = $this->getOrCreateConversation($request->sender_id, $request->receiver_id);

        $message = PrivateMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'content' => $request->content
        ]);

        return response()->json(['message' => $message]);
    }

    // Lấy tin nhắn của một cuộc trò chuyện
    public function getMessages($conversation_id)
    {
        $messages = PrivateMessage::where('conversation_id', $conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // Lấy danh sách cuộc trò chuyện của một user
    public function getUserConversations($user_id)
    {
        $conversations = Conversation::where('user1_id', $user_id)
            ->orWhere('user2_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['conversations' => $conversations]);
    }
}
