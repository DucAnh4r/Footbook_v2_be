<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reaction;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class ReactionController extends Controller
{
    /**
     * Add or update a reaction to a post
     */
    public function reactToPost(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:like,love,haha,sad,angry',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user already reacted to this post
        $existingReaction = Reaction::where('post_id', $request->post_id)
            ->where('user_id', $request->user_id)
            ->first();

        if ($existingReaction) {
            // Update existing reaction
            $existingReaction->type = $request->type;
            $existingReaction->save();

            return response()->json([
                'message' => 'Đã cập nhật cảm xúc thành công',
                'reaction' => $existingReaction
            ]);
        }

        // Create new reaction
        $reaction = Reaction::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Đã thêm cảm xúc thành công',
            'reaction' => $reaction
        ], 201);
    }

    /**
     * Remove a reaction from a post
     */
    public function removeReaction(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find and delete reaction
        $deleted = Reaction::where('post_id', $request->post_id)
            ->where('user_id', $request->user_id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Đã xóa cảm xúc thành công'
            ]);
        }

        return response()->json([
            'message' => 'Không tìm thấy cảm xúc để xóa'
        ], 404);
    }

    /**
     * Get reactions for a post
     */
    public function getPostReactions($post_id)
    {
        // Kiểm tra xem bài viết có tồn tại không
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Lấy danh sách reactions và thông tin user
        $reactions = Reaction::with('user')->where('post_id', $post_id)->get();

        // Đếm số lượng reactions theo loại
        $reactionCounts = [
            'like' => 0,
            'love' => 0,
            'haha' => 0,
            'sad' => 0,
            'angry' => 0,
            'total' => 0
        ];

        foreach ($reactions as $reaction) {
            $reactionCounts[$reaction->type]++;
            $reactionCounts['total']++;
        }

        return response()->json([
            'reactions' => $reactions,
            'counts' => $reactionCounts
        ]);
    }
}
