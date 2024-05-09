<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Feedback_Expert;
use App\Models\FeedbackExpert;
use Illuminate\Http\Request;
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
    public function createFeedbackExperts(Request $request)
{
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
    try {
        $feedbackExpert = $this->feedback->createFeedbackExperts($data);
        return response()->json([
            'success' => true,
            'message' => "Created feedback experts successfully",
            'data' => $feedbackExpert
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
