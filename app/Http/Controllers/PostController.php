<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isEmpty;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $posts = Post::with([
            'user',
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies.user','user');
            },
        ])->get();
        return response()->json([
            'success' => true,
            'message' => 'Show all posts successfully!',
            'data' => $posts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        $userId = $user->id;
        // Validate incoming request
        $validated = $request->validate([
            'content' => 'required|string',
            'is_anonymous' => 'required|boolean',
        ]);
        $data = [
            'user_id' => $userId,
            'content' => $validated['content'],
            'is_anonymous' => $request->is_anonymous,
        ];

        $post = Post::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Create post successfully',
            'post' => $post
        ], 201);
    }
    public function createPost(Request $request): JsonResponse
    {
        // Validate incoming request
        $validated = $request->validate([
            'user_id' => 'required',
            'content' => 'required|string',
            'is_anonymous' => 'required|boolean',
        ]);
        $data = [
            'user_id' => $validated['user_id'],
            'content' => $validated['content'],
            'is_anonymous' => $request->is_anonymous,
        ];

        $post = Post::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Create post successfully',
            'post' => $post
        ], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param $postId
     * @return JsonResponse
     */
    public function getOnePost($postId): JsonResponse
    {
        $post = Post::with([
            'user',
            'comments' => function ($query) {
                $query->whereNull('parent_id')->with('replies.user','user');
            }
        ])->findOrFail($postId);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Show post successfully!',
            'data' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function updatePostStatus(Request $request, $id = 0): JsonResponse
    {
        $post = Post::find($id);
        if (empty($post)) {
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data' => null,
            ], 404);
        }
        $newStatus = !($post->status);
        $post->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated status post successfully!',
            'data' => $post,
        ], 200);
    }
    public function updatePostContent(Request $request, $id = 0)
    {
        $user = $this->getUser($request);
        $userId = $user->id;
        $post = Post::where('id', $id)->where('user_id', $userId)->first();
        if (empty($post)) {
            return response()->json([
                'success' => false,
                'message' => 'user not match',
            ], 404);
        }
        // Validate incoming request
        $request->validate([
            'content' => 'required|string',
            'is_anonymous' => 'required|boolean',
        ]);
        $data = [
            'content' => $request['content'],
            'is_anonymous' => $request->is_anonymous,
        ];

        $post->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Update post successfully',
            'post' => $post
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return Response
     */
    public function destroy($id)
    {
        $post = Post::with('comments', 'comments.replies')->find($id);
        if (empty($post)) {
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data' => null,
            ], 404);
        }
        $post->comments()->delete();
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post and its comments deleted successfully!',
        ], 200);
    }

    public function deletePost(Request $request, $id)
{
    // Get the authenticated user
    $user = $this->getUser($request);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated',
        ], 401);
    }

    $userId = $user->id;

    // Retrieve the post with the specified ID and ensure it belongs to the authenticated user
    $post = Post::where('id', $id)->where('user_id', $userId)->with('comments', 'comments.replies')->first();
    if (empty($post)) {
        return response()->json([
            'success' => false,
            'message' => 'Post not found or does not belong to user',
        ], 404);
    }

    // Soft delete the comments and the post
    foreach ($post->comments as $comment) {
        $comment->delete();
    }
    $post->delete();

    return response()->json([
        'success' => true,
        'message' => 'Post and its comments deleted successfully!',
    ], 200);
}

    public function getAllPostsByUserId(Request $request)
    {
        $user = $this->getUser($request);
        if (!empty($user)) {
            $userId = $user->id;
            $posts = Post::with([
                'user',
                'comments' => function ($query) {
                    $query->whereNull('parent_id');
                },
                'comments.user'
            ])->where('user_id', $userId)
                ->get();
            if ($posts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No posts found for today',
                    'data' => null,
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Show all posts successfully!',
                'data' => $posts,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Please login',
            ], 401);
        }
    }

}
