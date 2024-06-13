<?php

namespace App\Http\Controllers;

use App\Models\LikePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikePostController extends Controller
{
    public function isLiked(Request $request, $postId)
    {
        $user = $this->getUser($request);
        $isLiked = LikePost::where('user_id', $user->id)->where('post_id', $postId)->exists();

        return response()->json(['isLiked' => $isLiked], 200);
    }
    public function like(Request $request,$postId)
    {
        $user = $this->getUser($request);
        $data = [
            'user_id' => $user->id,
            'post_id' => $postId,
        ];
        $likePost = LikePost::create($data);
        return response()->json([ 'success' => true,'message' => 'Post liked successfully', 'data' => $likePost], 200);
    }

    public function unlike(Request $request,$postId)
    {
        $user = $this->getUser($request);
        $likePost = LikePost::where('user_id', $user->id)->where('post_id', $postId)->first();

        if ($likePost) {
            $likePost->delete();
            return response()->json([ 'success' => true,'message' => 'Post unliked successfully'], 200);
        }

        return response()->json([ 'success' => false,'message' => 'Like not found'], 404);
    }
}
