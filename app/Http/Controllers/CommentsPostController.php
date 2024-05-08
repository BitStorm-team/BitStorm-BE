<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\CommentsPost;
use App\Models\Post;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


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
        $commentsPosts = CommentsPost::with('user', 'replies.user')->paginate(15);
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
    /**
     * Create a new comment post
     *
     * @OA\Post(
     *      path="/api/createComment",
     *      tags={"Comments"},
     *      summary="Create a new comment post",
     *      description="Create a new comment post with the provided data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"post_id", "user_id", "content"},
     *              @OA\Property(property="post_id", type="integer", format="int64", example="123", description="The ID of the post the comment belongs to"),
     *              @OA\Property(property="user_id", type="integer", format="int64", example="456", description="The ID of the user who posted the comment"),
     *              @OA\Property(property="content", type="string", example="This is a great post!", description="The content of the comment")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully created a comment post",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="Indicates whether the request was successful"),
     *              @OA\Property(property="message", type="string", example="Created comment post successfully!", description="A message describing the outcome of the request"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request. Invalid input data."
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized. Authentication is required."
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error. Failed to create the comment post."
     *      )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = [
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'content' => $request->content,
            'status' => 1
        ];
        $commentsPost = CommentsPost::create($data);
        return response()->json([
            'success' => true,
            'message' => 'Created comment post successfully!',
            'data' => $commentsPost,
        ], 200);
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
    public function update(Request $request, CommentsPost $commentsPost, $id)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'content' => 'required|string',
                'status' => 'required|integer', // Validate status as an integer
            ]);

            // Find comment post by id
            $comment = CommentsPost::findOrFail($id);

            // Update the comment
            $comment->update($validatedData);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Comment post updated successfully!',
                'data' => $comment,
            ], 200);
        } catch (ValidationException $e) {
            // Return validation error response
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->validator->errors(),
            ], 400);
        } catch (\Exception $e) {
            // Return error response for other exceptions
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment post',
                'error' => $e->getMessage(),
            ], 500);
        }
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
