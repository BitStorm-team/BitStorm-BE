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
    public function update(Request $request,$id)
    {
        $post=Post::find($id);
        $newStatus = !($post->status);
        $post->update([
            'status' => $newStatus,
        ]);

        // Trả về phản hồi thành công
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
    public function destroy(Post $post)
    {
        //
    }
}
