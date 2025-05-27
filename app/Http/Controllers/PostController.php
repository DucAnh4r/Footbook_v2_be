<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Relationship;
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
            'content' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'url',
            'group_id' => 'nullable|exists:groups,id',
            'privacy' => 'required|in:public,friends,secret',
            'image_url' => 'nullable|url' // For single image case
        ]);

        // Custom validation logic: content or images must exist
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
            'privacy' => $request->privacy,
            'isDeleted' => 0 // Explicitly set isDeleted to 0
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
        elseif ($request->filled('image_url')) {
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
            ->where('isDeleted', 0) // Only fetch non-deleted posts
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
    public function updatePostContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'privacy' => 'nullable|string',
            'theme' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::where('isDeleted', 0)->find($request->post_id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        if ($post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa bài viết này'], 403);
        }

        // Cập nhật nội dung
        $post->content = $request->content;
        if ($request->has('privacy')) {
            $post->privacy = $request->privacy;
        }
        if ($request->has('theme')) {
            $post->theme = $request->theme;
        }
        $post->save();

        // Xử lý ảnh nếu có
        if ($request->has('images')) {
            $newImageUrls = $request->images;

            // Lấy danh sách ảnh hiện tại
            $existingImages = PostImage::where('post_id', $post->id)->get();

            // Xóa ảnh không còn trong danh sách
            foreach ($existingImages as $image) {
                if (!in_array($image->image_url, $newImageUrls)) {
                    $image->delete();
                }
            }

            // Thêm ảnh mới nếu chưa tồn tại
            foreach ($newImageUrls as $url) {
                $exists = $existingImages->firstWhere('image_url', $url);
                if (!$exists) {
                    PostImage::create([
                        'post_id' => $post->id,
                        'image_url' => $url,
                        'created_at' => now(),
                    ]);
                }
            }
        }

        // Lấy lại danh sách ảnh mới
        $updatedImages = PostImage::where('post_id', $post->id)->get();

        return response()->json([
            'message' => 'Đã cập nhật bài viết thành công',
            'post' => $post,
            'images' => $updatedImages
        ]);
    }


    public function addImageToPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'image_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::where('isDeleted', 0)->find($request->post_id); // Only fetch non-deleted posts

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        if ($post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền thêm ảnh vào bài viết này'], 403);
        }

        $image = PostImage::create([
            'post_id' => $post->id,
            'image_url' => $request->image_url,
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Đã thêm ảnh vào bài viết thành công',
            'image' => $image
        ]);
    }

    public function deleteImageFromPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_id' => 'required|exists:post_images,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = PostImage::find($request->image_id);
        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy ảnh'], 404);
        }

        $post = Post::where('isDeleted', 0)->find($image->post_id); // Only fetch non-deleted posts
        if (!$post || $post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền xóa ảnh này'], 403);
        }

        $image->delete(); // Hard delete since PostImage has no isDeleted

        return response()->json([
            'message' => 'Đã xóa ảnh khỏi bài viết thành công'
        ]);
    }

    /**
     * Delete (soft) a post
     */
    public function deletePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::where('isDeleted', 0)->find($request->post_id); // Only fetch non-deleted posts

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        if ($post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền xóa bài viết này'], 403);
        }

        $post->isDeleted = 1; // Soft delete the post
        $post->save();

        return response()->json([
            'message' => 'Đã xóa bài viết thành công (xóa mềm)'
        ]);
    }

    /**
     * Get user's posts
     */
    public function getUserPosts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where('user_id', $request->user_id)
            ->where('isDeleted', 0) // Only fetch non-deleted posts
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'Không có bài viết nào phù hợp',
                'posts' => []
            ], 200);
        }

        return response()->json([
            'posts' => $posts
        ]);
    }

    /**
     * Get all posts of the authenticated user
     */
    public function getMyPosts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where('user_id', $request->user_id)
            ->where('isDeleted', 0) // Only fetch non-deleted posts
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'Bạn chưa có bài viết nào',
                'posts' => []
            ], 200);
        }

        return response()->json([
            'posts' => $posts
        ]);
    }

    /**
     * Get feed posts (posts from friends and groups)
     */
    public function getFeedPosts($user_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        $friendIds = $user->friends()->pluck('id')->toArray();
        $groupIds = $user->groups()->pluck('groups.id')->toArray();

        $posts = Post::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'avatar_url');
            },
            'images',
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with([
                    'user' => function ($q) {
                        $q->select('id', 'name', 'avatar_url');
                    },
                    'replies' => function ($q) {
                        $q->with(['user' => function ($q) {
                            $q->select('id', 'name', 'avatar_url');
                        }]);
                    }
                ]);
            },
            'reactions' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'type')
                    ->with(['user' => function ($q) {
                        $q->select('id', 'name', 'avatar_url');
                    }]);
            },
            'originalPost' => function ($query) {
                $query->where('isDeleted', 0) // Only fetch non-deleted original posts
                    ->with([
                        'user' => function ($q) {
                            $q->select('id', 'name', 'avatar_url');
                        },
                        'images',
                        'comments' => function ($q) {
                            $q->whereNull('parent_id')->with([
                                'user' => function ($q) {
                                    $q->select('id', 'name', 'avatar_url');
                                },
                                'replies' => function ($q) {
                                    $q->with(['user' => function ($q) {
                                        $q->select('id', 'name', 'avatar_url');
                                    }]);
                                }
                            ]);
                        },
                        'reactions' => function ($q) {
                            $q->select('id', 'post_id', 'user_id', 'type')
                                ->with(['user' => function ($q) {
                                    $q->select('id', 'name', 'avatar_url');
                                }]);
                        }
                    ]);
            },
            'group' => function ($query) {
                $query->select('id', 'name');
            }
        ])
            ->where('isDeleted', 0) // Only fetch non-deleted posts
            ->where(function ($query) use ($user_id, $friendIds, $groupIds) {
                $query->where('privacy', 'public');
                if (!empty($friendIds)) {
                    $query->orWhere(function ($q) use ($friendIds) {
                        $q->whereIn('user_id', $friendIds)
                            ->where('privacy', 'friends');
                    });
                }
                $query->orWhere('user_id', $user_id);
                if (!empty($groupIds)) {
                    $query->orWhere(function ($q) use ($groupIds) {
                        $q->whereIn('group_id', $groupIds);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $posts->each(function ($post) {
            if ($post->shareId && $post->originalPost) {
                $post->originalPost->makeHidden(['isDeleted', 'shareId']);
            } elseif ($post->shareId) {
                $post->originalPost = [
                    'isDeleted' => true,
                    'message' => 'Bài viết gốc đã bị xóa hoặc không tồn tại'
                ];
            }
        });

        if ($posts->isEmpty()) {
            return response()->json([
                'message' => 'Không có bài viết nào trong feed',
                'posts' => []
            ], 200);
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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:9',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $limit = $request->limit ?? 20;
        $offset = $request->offset ?? 0;

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $posts = Post::where('user_id', $request->user_id)
            ->where('isDeleted', 0) // Only fetch non-deleted posts
            ->with('images')
            ->has('images')
            ->orderBy('created_at', 'desc')
            ->get();

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
        $validator = Validator::make($request->all(), [
            'user view_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'content' => 'nullable|string',
            'privacy' => 'required|in:public,friends,secret',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $originalPost = Post::where('isDeleted', 0)->find($request->post_id); // Only fetch non-deleted posts
        if (!$originalPost) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $sharedPost = Post::create([
            'user_id' => $request->user_id,
            'content' => $request->content,
            'shareId' => $request->post_id,
            'group_id' => $request->group_id,
            'created_at' => now(),
            'privacy' => $request->privacy,
            'isDeleted' => 0 // Explicitly set isDeleted to 0
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
            ->where('isDeleted', 0) // Only fetch non-deleted posts
            ->find($id);

        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        if ($post->shareId) {
            $originalPost = Post::with(['user', 'images', 'comments', 'reactions'])
                ->where('isDeleted', 0) // Only fetch non-deleted original posts
                ->find($post->shareId);

            $post->originalPost = $originalPost ?: [
                'isDeleted' => true,
                'message' => 'Bài viết gốc đã bị xóa hoặc không tồn tại'
            ];
        }

        return response()->json([
            'post' => $post
        ]);
    }

    /**
     * Get share count and details for a post
     */
    public function getPostShares($post_id)
    {
        $post = Post::where('isDeleted', 0)->find($post_id); // Only fetch non-deleted posts
        if (!$post) {
            return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
        }

        $sharedPosts = Post::with(['user'])
            ->where('shareId', $post_id)
            ->where('isDeleted', 0) // Only fetch non-deleted shared posts
            ->orderBy('created_at', 'desc')
            ->get();

        $shareCount = $sharedPosts->count();

        return response()->json([
            'post_id' => (int)$post_id,
            'share_count' => $shareCount,
            'shared_posts' => $sharedPosts
        ]);
    }
}
