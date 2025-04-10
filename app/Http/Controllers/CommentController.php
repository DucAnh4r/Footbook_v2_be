<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Add a comment to a post
     */
    public function addComment(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create comment
        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'content' => $request->content,
            'created_at' => now(),
        ]);

        // Get comment with user
        $comment = Comment::with('user')->find($comment->id);

        return response()->json([
            'message' => 'Đã thêm bình luận thành công',
            'comment' => $comment
        ], 201);
    }

    /**
     * Update a comment
     */
    public function updateComment(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find comment
        $comment = Comment::find($request->comment_id);

        // Check if user is the owner of the comment
        if ($comment->user_id != $request->user_id) {
            return response()->json([
                'message' => 'Bạn không có quyền chỉnh sửa bình luận này'
            ], 403);
        }

        // Update comment
        $comment->content = $request->content;
        $comment->save();

        return response()->json([
            'message' => 'Đã cập nhật bình luận thành công',
            'comment' => $comment
        ]);
    }

    /**
     * Delete a comment
     */
    public function deleteComment(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find comment
        $comment = Comment::find($request->comment_id);

        // Check if user is the owner of the comment
        if ($comment->user_id != $request->user_id) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa bình luận này'
            ], 403);
        }

        // Delete comment
        $comment->delete();

        return response()->json([
            'message' => 'Đã xóa bình luận thành công'
        ]);
    }

    /**
     * Get comments for a post
     */
    public function getPostComments(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Get comments
        $comments = Comment::with('user')
            ->where('post_id', $request->post_id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    /**
     * Get comment count for a post
     */
    public function getPostCommentCount(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Đếm số lượng comments theo post_id
        $count = Comment::where('post_id', $request->post_id)->count();

        return response()->json([
            'post_id' => $request->post_id,
            'comment_count' => $count
        ]);
    }
}
