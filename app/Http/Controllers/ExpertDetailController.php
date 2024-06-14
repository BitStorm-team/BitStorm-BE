<?php

namespace App\Http\Controllers;

use App\Models\ExpertDetail;
use App\Models\Calendar;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Nette\Schema\Expect;
use Illuminate\Support\Facades\DB;

class ExpertDetailController extends Controller
{
    protected $experts;
    public function __construct()
    {
        $this->experts = new ExpertDetail();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/expertdetail",
     *     summary="Display all expert form database",
     *      tags={"Expert Details"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $experts = $this->experts->getAllExpert();
        if ($experts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Experts not found',
                'data' => null,
            ], 404);
        };
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => $experts
        ], 200);
    }

    // Get expert details
    /**
     * @OA\Get(
     *     path="/api/expert/{id}",
     *     summary="Get one expert detail ",
     *     tags={"Expert Details"},
     *          @OA\Parameter(
     *              name="id",
     *               in="path",
     *              description="Expert ID",
     *              required=true,
     *              @OA\Schema(type="integer")
     *          ),
     *     @OA\Response(response=200, description="success"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getExpertDetail($id)
    {
        // Bước 1: Lấy chi tiết của chuyên gia dựa trên id
        $expertDetail = ExpertDetail::where('user_id', $id)->with("calendars")->first();
        if (!$expertDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Expert not found!',
            ], 404);
        }
        // Bước 2:

        $calendars = Calendar::where('expert_id', $id)->get();
        $feedback = DB::table('bookings')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            ->join('feedback_experts', 'bookings.id', '=', 'feedback_experts.booking_id')
            ->join('calendars', 'calendars.id', '=', 'bookings.calendar_id')
            ->where('calendars.expert_id', '=', $id)
            ->select('feedback_experts.*', 'users.name', 'users.profile_picture')
            ->get();

        // Kết hợp thông tin từ $user và $expertDetail vào một mảng
        $data = [
            'expertDetail' => $expertDetail,
            'schedules' => $calendars,
            'feedback' => $feedback
        ];
        // Trả về view với dữ liệu đã lấy được
        return response()->json([
            'success' => true,
            'message' => 'Get detail expert successfully!',
            'data' => $data,
        ], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/experts",
     *     summary="Display all expert form database and display in the website",
     *      tags={"Expert Details"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getListExpert()
    {
        $experts = $this->experts->getListExpert();

        if ($experts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Experts not found',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'total' => 15,
            'per_page' => 5,
            'current_page' => 1,
            'last_page' => 4,
            'first_page_url' => null,
            'last_page_url' => null,
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => "",
            'from' => 1,
            'to' => 10,
            'data' => [
                $experts
            ],
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/expert/expert-profile/{id}",
     *     summary="Display expert profile",
     *     tags={"Expert profile"},
     *     @OA\Parameter(
     *              name="id",
     *              in="path",
     *              description="Expert ID",
     *              required=true,
     *              @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($id)
    {
        $expert = $this->experts->getExpertProfile($id);
        if (empty($expert)) {
            return response()->json([
                'success' => false,
                'message' => 'ExpertID not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Show the expert successfully!',
            'data' => $expert,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpertDetail $expertDetail)
    {
        //
    }
    public function updateExpertProfile(Request $request)
    {
        $expert = $this->getUser($request);
        $expertID = $expert->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'profile_picture' => 'string|url',
            'date_of_birth' => 'date',
            'phone_number' => [
                'numeric',
                'digits:10', // Ensure phone number has 10 digits
                'regex:/^(0)[0-9]{9}$/', // Ensure phone number starts with 0 and is followed by 9 digits
            ],
            'gender' => 'string',
            'experience' => 'string',
            'certificate' => 'string|url'
        ]);

        if (empty($expertID)) {
            return response()->json([
                'success' => false,
                'message' => 'Expert ID not found',
            ], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400); // Bad request
        }

        // Check if the email already exists for another user
        $existingExpert = User::where('email', $request->input('email'))->where('id', '!=', $expertID)->first();
        if ($existingExpert) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists in the system.',
            ], 400); // Bad request
        }

        // Update expert information
        $expert->name = $request->input('name');
        $expert->email = $request->input('email');
        $expert->address = $request->input('address'); // Make sure to set the address
        $expert->phone_number = $request->input('phone_number');
        $expert->gender = $request->input('gender');
        $expert->date_of_birth = $request->input('date_of_birth');
        $expert->profile_picture = $request->input('profile_picture');
        $expert->status = 1;
        $expert->save();

        // Update expert details
        $expertDetail = ExpertDetail::where('user_id', $expertID)->first();
        $expertDetail->experience = $request->input('experience');
        $expertDetail->certificate = $request->input('certificate');
        $expertDetail->save();

        $expert = $this->experts->getExpertProfile($expertID);
        return response()->json([
            'success' => true,
            'message' => 'Expert updated successfully',
            'data' => $expert,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpertDetail $expertDetail)
    {
        //
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('searchTerm');

        if ($searchTerm) {
            $experts = User::where('role_id', 3)
                ->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                        ->orWhere('email', 'like', "%$searchTerm%");
                })
                ->with('expert')
                ->get();

            if ($experts->isEmpty()) {
                $experts = ExpertDetail::where('experience', 'like', "%$searchTerm%")
                    ->whereHas('user', function ($query) {
                        $query->where('role_id', 3);
                    })
                    ->with('user')
                    ->get();
            }
        } else {
            return response()->json(['message' => 'No search term provided'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Experts retrieved successfully',
            'data' => $experts
        ], 200);
    }


    // filter
    public function filter(Request $request)
    {
        // Get all query parameters for filtering
        $query = Calendar::query();
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Execute the query and get the results
        $calendar = DB::table('bookings')
            ->join('calendars', 'calendars.id', '=', 'bookings.calendar_id')
            ->join('users', 'users.id', '=', 'bookings.user_id')
            // ->join('expert_details', 'expert_details.user_id', '=', 'users.id')
            ->select('calendars.*', 'users.*')
            ->whereBetween('calendars.price', [$minPrice, $maxPrice])
            ->get();
        // Return the filtered results
        return response()->json([
            'success' => true,
            'data' => $calendar,
        ]);
    }
    function getOwnCalendars(Request $request, $id)
    {
        return "hello";
    }
}
