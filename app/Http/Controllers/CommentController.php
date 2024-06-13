<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class CommentController extends Controller
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
        $commentsPosts = Comment::with('user', 'replies.user')->paginate(10);
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
    public function store(Request $request, $postId)
    {
        //  lấy user hiện tại
        $user = $this->getUser($request);
        try {
            $validator = $request->validate([
                'content' => 'required|string',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            $data = [
                'post_id' => $postId,
                'user_id' => $user->id,
                'content' => $validator['content'],
                'status' => 1,
                'parent_id' => $request->parent_id,
            ];
            $comment = Comment::create($data);
            $comment2 = Comment::with([
                'user',
                'replies' => function ($query) {
                    $query->whereNull('parent_id')->with('replies.user', 'user');
                },
            ])->where('id', $comment->id)->first();
            return response()->json([
                'success' => true,
                'message' => 'Created comment post successfully!',
                'data' => $comment2,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param Comment $commentsPost
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $commentsPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Comment $commentsPost
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *    path="/api/comments/update/{id}",
     *      tags={"Comments"},
     *      summary="Update a comment post by ID",
     *      description="Update a comment post by its ID",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *        @OA\JsonContent(
     *              required={"content", "status"},
     *              @OA\Property(property="content", type="string", example="Updated comment content", description="Content of the comment post"),
     *              @OA\Property(property="status", type="integer", example=1, description="Status of the comment post"),
     *          )
     *     ),
     *    @OA\Response(
     *          response=400,
     *          description="Validation error. Invalid input data."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found. The comment post with the specified ID does not exist."
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error. Failed to update the comment post."
     *      ),
     *     security={{"bearerAuth":{}}},
     *     @OA\SecurityScheme(
     *         securityScheme="X-CSRF-TOKEN",
     *         type="apiKey",
     *         in="header",
     *         name="X-CSRF-TOKEN",
     *         description="CSRF Token"
     *     )
     * )
     */


    public function update(Request $request, $postId, $commentId)
    {
        // return response()->json($request->content);
        try {
            // Validate request data
            $validatedData = $request->validate([
                'content' => 'required|string',
                'status' => 'required|integer', // Validate status as an integer
            ]);

            // Authenticate user
            $user = $this->getUser($request);

            // Find comment post by id and post_id
            $comment = Comment::where('id', $commentId)
                ->where('post_id', $postId)
                ->firstOrFail();

            // Check if the authenticated user is the owner of the comment
            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to update this comment',
                ], 403);
            }

            // Update the comment
            $comment->update($validatedData);
            $comment2 = Comment::with([
                'user',
                'replies' => function ($query) {
                    $query->whereNull('parent_id')->with('replies.user', 'user');
                },
            ])->where('id', $comment->id)->first();
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Comment post updated successfully!',
                'data' => $comment2,
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
     * @param Comment $commentsPost
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *     path="/api/deleteComment/{post_id}/{user_id}",
     *     summary="Delete Comment Post",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="post_id",
     *         in="path",
     *         description="Comment Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Comment not found"),
     *     @OA\Response(response=500, description="Internal Server Error"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function destroy(Request $request, $postId, $commentId)
    {
        $user = $this->getUser($request);
        $comment = Comment::where('id', $commentId)
            ->where('post_id', $postId)
            ->where('user_id', $user->id)
            ->firstOrFail();


        if ($comment) {
            $comment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'post_id or user_id not found',
            ], 404);
        }
    }

    public function createPostByAdmin(Request $request)
    {
        // Get the current user
        $user = $this->getUser($request);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
            'content' => 'required',
            'status' => 'required|integer', // Ensure status is an integer
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the comment data array
        $data = [
            'post_id' => $request->post_id,
            'user_id' => $request->user_id,
            'content' => $request->content,
            'status' => $request->status,
        ];

        // Create the comment post
        $commentsPost = Comment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created comment post successfully!',
            'data' => $commentsPost,
        ], 200);
    }

    public function updatePostByAdmin(Request $request, $id)
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'status' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find comment post by id
            $comment = Comment::where('id', $id)->firstOrFail();

            // Update the comment with validated data
            $comment->update($validator->validated());

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Comment post updated successfully!',
                'data' => $comment,
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Return error response if comment not found
            return response()->json([
                'success' => false,
                'message' => 'Comment post not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            // Return error response for other exceptions
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroyPostByAdmin(Request $request, $id)
    {
        try {
            // Find the comment post by id
            $comment = Comment::where('id', $id)->firstOrFail();

            // Soft delete the comment
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comment post not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
