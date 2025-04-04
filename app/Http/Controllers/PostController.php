<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Create a new post
     */
    public function createPost(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create post
        $post = Post::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
            'group_id' => $request->group_id,
            'created_at' => now(),
        ]);

        // Add image if provided
        if ($request->has('image_url')) {
            $postImage = PostImage::create([
                'post_id' => $post->id,
                'image_url' => $request->image_url,
                'created_at' => now(),
            ]);
            
            // Return post with image
            return response()->json([
                'message' => 'Đã tạo bài viết thành công',
                'post' => $post,
                'image' => $postImage
            ], 201);
        }

        return response()->json([
            'message' => 'Đã tạo bài viết thành công',
            'post' => $post
        ], 201);
    }

    /**
     * Get post details
     */
    public function getPost($id)
    {
        $post = Post::with(['user', 'images', 'comments', 'reactions'])
                    ->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        return response()->json([
            'post' => $post
        ]);
    }

    /**
     * Update a post
     */
    public function updatePost(Request $request)
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

        // Find post
        $post = Post::find($request->post_id);

        // Check if user is the owner of the post
        if ($post->user_id != $request->user_id) {
            return response()->json([
                'message' => 'Bạn không có quyền chỉnh sửa bài viết này'
            ], 403);
        }

        // Update post
        $post->content = $request->content;
        $post->save();

        return response()->json([
            'message' => 'Đã cập nhật bài viết thành công',
            'post' => $post
        ]);
    }

    /**
     * Delete a post
     */
    public function deletePost(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find post
        $post = Post::find($request->post_id);

        // Check if user is the owner of the post
        if ($post->user_id != $request->user_id) {
            return response()->json([
                'message' => 'Bạn không có quyền xóa bài viết này'
            ], 403);
        }

        // Delete post (this will also delete associated images, comments, and reactions through cascade)
        $post->delete();

        return response()->json([
            'message' => 'Đã xóa bài viết thành công'
        ]);
    }

    /**
     * Get user's posts
     */
    public function getUserPosts(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Get posts
        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
                    ->where('user_id', $request->user_id)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

        return response()->json([
            'posts' => $posts
        ]);
    }

    /**
     * Get feed posts (posts from friends and groups)
     */
    public function getFeedPosts(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Get user and friends
        $user = User::find($request->user_id);
        $friends = $user->friends()->pluck('id')->toArray();
        
        // Add user's own ID to the list
        $friends[] = $user->id;

        // Get user's groups
        $groups = $user->groups()->pluck('id')->toArray();

        // Get posts from friends and groups
        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
                    ->where(function ($query) use ($friends, $groups) {
                        $query->whereIn('user_id', $friends)
                              ->orWhereIn('group_id', $groups);
                    })
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();

        return response()->json([
            'posts' => $posts
        ]);
    }
}