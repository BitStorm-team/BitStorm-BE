<?php

namespace App\Http\Controllers;

use App\Models\LikePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikePostController extends Controller
{

    public function getLikedPosts(Request $request)
    {
        try {
            $user = $this->getUser($request);

            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $isLiked = LikePost::where('user_id', $user->id)->get();

            return response()->json(['data' => $isLiked], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function like(Request $request, $postId)
    {
        try {
            $user = $this->getUser($request);
            $data = [
                'user_id' => $user->id,
                'post_id' => $postId,
            ];

            if (LikePost::where($data)->exists()) {
                return response()->json(['success' => false, 'message' => 'You have already liked this post'], 400);
            }

            $likePost = LikePost::create($data);

            return response()->json(['success' => true, 'message' => 'Post liked successfully', 'data' => $likePost], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function unlike(Request $request, $postId)
    {
        try {
            $user = $this->getUser($request);
            $likePost = LikePost::where('user_id', $user->id)->where('post_id', $postId)->first();
            $likePost->delete();

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
