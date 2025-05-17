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
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra nếu có parent_id thì phải là comment gốc (không phải reply)
        if ($request->has('parent_id') && $request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            
            // Kiểm tra xem parent_id có phải là bình luận gốc không
            if ($parentComment && !is_null($parentComment->parent_id)) {
                return response()->json([
                    'message' => 'Không thể trả lời cho một bình luận đã là trả lời'
                ], 400);
            }
        }

        // Create comment
        $comment = Comment::create([
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'created_at' => now(),
        ]);

        // Get comment with user
        $comment = Comment::with('user')->find($comment->id);

        return response()->json([
            'message' => $request->has('parent_id') ? 'Đã thêm trả lời thành công' : 'Đã thêm bình luận thành công',
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
     * Get comments for a post (chỉ lấy comment gốc)
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

        // Get parent comments (không có parent_id)
        $comments = Comment::with('user')
            ->where('post_id', $request->post_id)
            ->whereNull('parent_id') // Chỉ lấy comment gốc
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Đếm số lượng replies cho mỗi comment
        foreach ($comments as $comment) {
            $comment->reply_count = Comment::where('parent_id', $comment->id)->count();
        }

        return response()->json([
            'comments' => $comments
        ]);
    }

    /**
     * Get replies for a comment
     */
    public function getCommentReplies(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Kiểm tra xem comment_id có phải là comment gốc không
        $comment = Comment::find($request->comment_id);
        if (!$comment->isParent()) {
            return response()->json([
                'message' => 'Comment ID phải là comment gốc'
            ], 400);
        }

        // Get replies
        $replies = Comment::with('user')
            ->where('parent_id', $request->comment_id)
            ->orderBy('created_at', 'asc') // Hiển thị từ cũ đến mới
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'replies' => $replies
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

        // Đếm tổng số lượng comments (bao gồm cả replies)
        $count = Comment::where('post_id', $request->post_id)->count();
        
        // Đếm số lượng comments gốc
        $parentCount = Comment::where('post_id', $request->post_id)
                             ->whereNull('parent_id')
                             ->count();
        
        // Đếm số lượng replies
        $replyCount = $count - $parentCount;

        return response()->json([
            'post_id' => $request->post_id,
            'total_comment_count' => $count,
            'parent_comment_count' => $parentCount,
            'reply_count' => $replyCount
        ]);
    }
}