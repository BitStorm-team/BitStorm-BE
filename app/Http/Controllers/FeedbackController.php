<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Feedback_Expert;
use App\Models\FeedbackExpert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
class FeedbackController extends Controller
{   
    protected $feedback;
    public function __construct()
    {
        $this->feedback = new FeedbackExpert();
    }
    public function getAllFeedbacks(){
        $feedbackExperts = FeedbackExpert::with('booking')->get();
        return response()->json([
            'success' => true,
            'message' => "Created feedback experts successfully",
            'data' => $feedbackExperts
        ]);
    }
/**
* @OA\Post(
     *     path="/api/feedback",
     *     summary="Feedback about a expert",
     *    tags={"Feedback experts"},
     *     @OA\Parameter(
     *         name="booking_id",
     *         in="query",
     *         description="Booking Id from booking",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         description="Content of feedback experts",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="Rating from 1-5",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="401", description="Not found")
     * )
     */

    public function createFeedbackExpert(Request $request)
{
    $user = $this->getUser($request);
    $validator = Validator::make($request->all(), [
        'booking_id' => 'required',
        'content' => ['required', 'regex:/^\S.*\S$/'],
        'rating' => ['required', 'numeric', 'between:1,5']
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }
    $data = [
        'booking_id' => $request->booking_id,
        'content' => $request->content,
        'rating' => $request->rating,
    ];
    $feedbackExpert = DB::table('feedback_experts')->insert($data);
    try {
        return response()->json([
            'success' => true,
            'message' => "Created feedback experts successfully",
            'data' => [
                $feedbackExpert,
                $data,
                $user
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating feedback experts',$data,
            'error' => $e->getMessage()
        ]);
    }
}

}
