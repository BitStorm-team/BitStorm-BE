<?php

namespace App\Http\Controllers;

use App\Models\CommentsPost;
use Illuminate\Http\Request;

class CommentsPostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/comments",
     *     summary="Get all comments",
     *     tags={"Comments"},
     *     @OA\Response(response=200, description="All Comments"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $commentsPosts=CommentsPost::with('user','replies.user')->paginate(15);
        return response()->json([
            'success' => true,
            'message' => 'Show all comments successfully!',
            'data' => $commentsPosts,
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
     * @param  \App\Models\CommentsPost  $commentsPost
     * @return \Illuminate\Http\Response
     */
    public function show(CommentsPost $commentsPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CommentsPost  $commentsPost
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CommentsPost $commentsPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CommentsPost  $commentsPost
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommentsPost $commentsPost)
    {
        //
    }
}
