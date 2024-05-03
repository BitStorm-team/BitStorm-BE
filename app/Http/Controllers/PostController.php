<?php

namespace App\Http\Controllers;

use App\Models\CommentsPost;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user','comments.user','comments.replies.user')->where('status',1)->get();
        return response()->json([
            'success' => true,
            'message' => 'Show all posts successfully!',
            'data' => $posts,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($postId)
    {
        $post = Post::with('user','comments.user','comments.replies.user')->find($postId);
        if(empty($post)){
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data'=> null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Show post successfully!',
            'data' => $post,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function updatePostStatus(Request $request,$id=0)
    {
        $post=Post::find($id);
        if(empty($post)){
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data'=> null,
            ], 404);
        }
        $newStatus = !($post->status);
        $post->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated status post successfully!',
            'data'=> $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::with('comments', 'comments.replies')->find($id);
        if(empty($post)){
            return response()->json([
                'success' => false,
                'message' => 'PostID not found',
                'data'=> null,
            ], 404);
        }
        $post->comments()->delete();
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post and its comments deleted successfully!',
            'data' => null,
        ], 200);
    }
}