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
        // Validate input data cơ bản
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'group_id' => 'nullable|exists:groups,id',
            'privacy' => 'required|in:public,friends,secret',
            'image_url' => 'nullable|url' // Cho trường hợp ảnh đơn
        ]);

        // Custom validation logic: content hoặc images phải tồn tại
        $validator->after(function ($validator) use ($request) {
            $hasContent = $request->filled('content');
            $hasImages = $request->has('images') && is_array($request->images) && count($request->images) > 0;
            $hasSingleImage = $request->filled('image_url');

            if (!$hasContent && !$hasImages && !$hasSingleImage) {
                $validator->errors()->add('content', 'Vui lòng nhập nội dung hoặc ít nhất một ảnh.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create post
        $post = Post::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
            'group_id' => $request->group_id,
            'created_at' => now(),
            'privacy' => $request->privacy
        ]);

        $postImages = [];

        // Add images if provided
        if ($request->has('images') && is_array($request->images)) {
            foreach ($request->images as $imageUrl) {
                $postImage = PostImage::create([
                    'post_id' => $post->id,
                    'image_url' => $imageUrl,
                    'created_at' => now(),
                ]);
                $postImages[] = $postImage;
            }
        }
        // Single image_url fallback
        elseif ($request->has('image_url')) {
            $postImage = PostImage::create([
                'post_id' => $post->id,
                'image_url' => $request->image_url,
                'created_at' => now(),
            ]);
            $postImages[] = $postImage;
        }

        return response()->json([
            'message' => 'Đã tạo bài viết thành công',
            'post' => $post,
            'images' => $postImages
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
            'images' => 'nullable|array',
            'images.*' => 'url',
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

        // Update post content
        $post->content = $request->content;
        $post->save();

        // Update images if provided
        if ($request->has('images')) {
            // Delete existing images
            PostImage::where('post_id', $post->id)->delete();

            // Add new images
            $postImages = [];
            foreach ($request->images as $imageUrl) {
                $postImage = PostImage::create([
                    'post_id' => $post->id,
                    'image_url' => $imageUrl,
                    'created_at' => now(),
                ]);
                $postImages[] = $postImage;
            }

            return response()->json([
                'message' => 'Đã cập nhật bài viết thành công',
                'post' => $post,
                'images' => $postImages
            ]);
        }

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
    public function getFeedPosts($user_id, Request $request)
    {
        // Validate chỉ limit và offset
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate user_id
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Get user and friends
        $user = User::find($request->user_id);
        $friends = $user->friends()->pluck('id')->toArray();

        // Get posts
        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where(function ($query) use ($user, $friends) {
                // Bài viết công khai
                $query->where('privacy', 'public');

                // Hoặc bài viết từ bạn bè có privacy là friends
                if (!empty($friends)) {
                    $query->orWhere(function ($q) use ($friends) {
                        $q->whereIn('user_id', $friends)
                            ->where('privacy', 'friends');
                    });
                }

                // Hoặc bài viết của chính người dùng (bao gồm tất cả privacy levels)
                $query->orWhere('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Kiểm tra nếu không có bài viết nào
        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'Không có bài viết nào trong feed',
                'posts' => []
            ], 200); // Trả về 200 thay vì 404 vì đây là tình huống hợp lệ
        }

        return response()->json([
            'posts' => $posts
        ]);
    }


    /**
     * Get all images from a specific user
     */
    public function getUserImages(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:9',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 20;
        $offset = $request->offset ?? 0;

        // Find user
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // Get posts by this user
        $posts = Post::where('user_id', $request->user_id)
            ->with('images')
            ->has('images')
            ->orderBy('created_at', 'desc')
            ->get();

        // Extract all images with their post information
        $images = [];
        foreach ($posts as $post) {
            foreach ($post->images as $image) {
                $images[] = [
                    'image_id' => $image->id,
                    'image_url' => $image->image_url,
                    'post_id' => $post->id,
                    'user_id' => $post->user_id,
                    'created_at' => $image->created_at
                ];
            }
        }

        // Apply pagination
        $paginatedImages = array_slice($images, $offset, $limit);

        return response()->json([
            'user_id' => $request->user_id,
            'total_images' => count($images),
            'images' => $paginatedImages
        ]);
    }

    /**
     * Share an existing post
     */
    public function sharePost(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'nullable|string',
            'privacy' => 'required|in:public,friends,secret',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if post exists and is not deleted
        $originalPost = Post::find($request->post_id);
        if (!$originalPost) {
            return response()->json([
                'message' => 'Không tìm thấy bài viếttttttttttttttttttttt'
            ], 404);
        }

        if ($originalPost->isDeleted == 1) {
            return response()->json([
                'message' => 'Bài viết gốc đã bị xóa'
            ], 400);
        }

        // Create shared post
        $sharedPost = Post::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
            'shareId' => $request->post_id,
            'group_id' => $request->group_id,
            'created_at' => now(),
            'privacy' => $request->privacy,
            'isDeleted' => 0
        ]);

        return response()->json([
            'message' => 'Đã chia sẻ bài viết thành công',
            'post' => $sharedPost
        ], 201);
    }

    /**
     * Get shared post with original post details
     */
    public function getSharedPost($id)
    {
        $post = Post::with(['user', 'comments', 'reactions'])
            ->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        // If this is a shared post, get the original post data
        if ($post->shareId) {
            $originalPost = Post::with(['user', 'images', 'comments', 'reactions'])
                ->find($post->shareId);

            // Check if original post exists and is not deleted
            if ($originalPost && !$originalPost->isDeleted) {
                $post->originalPost = $originalPost;
            } else {
                $post->originalPost = [
                    'isDeleted' => true,
                    'message' => 'Bài viết gốc đã bị xóa hoặc không tồn tại'
                ];
            }
        }

        return response()->json([
            'post' => $post
        ]);
    }
}
