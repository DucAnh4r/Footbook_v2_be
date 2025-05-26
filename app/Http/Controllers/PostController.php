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
    public function updatePostContent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $post = Post::find($request->post_id);

        if ($post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa bài viết này'], 403);
        }

        $post->content = $request->content;
        $post->save();

        return response()->json([
            'message' => 'Đã cập nhật nội dung bài viết thành công',
            'post' => $post
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

        $post = Post::find($request->post_id);

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
        $post = $image->post;

        if (!$post || $post->user_id != $request->user_id) {
            return response()->json(['message' => 'Bạn không có quyền xóa ảnh này'], 403);
        }

        $image->delete();

        return response()->json([
            'message' => 'Đã xóa ảnh khỏi bài viết thành công'
        ]);
    }



    /**
     * Delete (soft) a post
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

        // Set isDeleted = true instead of deleting the post
        $post->isDeleted = true;
        $post->save();

        return response()->json([
            'message' => 'Đã xóa bài viết thành công (xóa mềm)'
        ]);
    }


    /**
     * Get user's posts
     */
    /**
     * Get user's posts based on relationship with the requester
     */
    public function getUserPosts(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'myId' => 'required|exists:users,id',
            'limit' => 'nullable|integer|min:1',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Set pagination parameters
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;

        // Find user
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        // Check relationship status
        $relationship = Relationship::where(function ($query) use ($request) {
            $query->where('requester_id', $request->myId)
                ->where('addressee_id', $request->user_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('requester_id', $request->user_id)
                ->where('addressee_id', $request->myId);
        })->first();

        // Build query for posts
        $query = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where('user_id', $request->user_id)
            ->where('isDeleted', false)
            ->orderBy('created_at', 'desc');

        // If myId is the same as user_id, get all posts (including secret)
        if ($request->myId == $request->user_id) {
            // No privacy filter needed
        }
        // If they are friends, get public and friends posts
        elseif ($relationship && $relationship->status == 'accepted') {
            $query->whereIn('privacy', ['public', 'friends']);
        }
        // If not friends, get only public posts
        else {
            $query->where('privacy', 'public');
        }

        // Apply pagination
        $posts = $query->limit($limit)
            ->offset($offset)
            ->get();

        // Check if no posts are found
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

        // Find user
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        // Get all posts of the user, including all privacy levels
        $posts = Post::with(['user', 'images', 'comments', 'reactions'])
            ->where('user_id', $request->user_id)
            ->where('isDeleted', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Check if no posts are found
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
        // Validate input data
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

        // Get friend IDs
        $friendIds = $user->friends()->pluck('id')->toArray();

        // Get group IDs that the user is a member of
        $groupIds = $user->groups()->pluck('groups.id')->toArray();

        // Build query for posts
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
                $query->where('isDeleted', false)
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
        ->where('isDeleted', false)
        ->where(function ($query) use ($user_id, $friendIds, $groupIds) {
            // Public posts from anyone
            $query->where('privacy', 'public');

            // Friends' posts with 'friends' privacy
            if (!empty($friendIds)) {
                $query->orWhere(function ($q) use ($friendIds) {
                    $q->whereIn('user_id', $friendIds)
                      ->where('privacy', 'friends');
                });
            }

            // User's own posts (all privacy levels)
            $query->orWhere('user_id', $user_id);

            // Posts from groups the user is a member of
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

        // Process shared posts to include original post details
        $posts->each(function ($post) {
            if ($post->shareId && $post->originalPost) {
                // Original post exists and is not deleted
                $post->originalPost->makeHidden(['isDeleted', 'shareId']);
            } elseif ($post->shareId) {
                // Original post is deleted or doesn't exist
                $post->originalPost = [
                    'isDeleted' => true,
                    'message' => 'Bài viết gốc đã bị xóa hoặc không tồn tại'
                ];
            }
        });

        // Check if no posts are found
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

    /**
     * Get share count and details for a post
     */
    public function getPostShares($post_id)
    {
        // Validate if post exists
        $post = Post::find($post_id);
        if (!$post) {
            return response()->json([
                'message' => 'Không tìm thấy bài viết'
            ], 404);
        }

        // Get shared posts
        $sharedPosts = Post::with(['user'])
            ->where('shareId', $post_id)
            ->where('isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Count shares
        $shareCount = $sharedPosts->count();

        return response()->json([
            'post_id' => (int)$post_id,
            'share_count' => $shareCount,
            'shared_posts' => $sharedPosts
        ]);
    }
}
