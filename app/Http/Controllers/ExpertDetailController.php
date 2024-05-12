<?php

namespace App\Http\Controllers;

use App\Models\ExpertDetail;
use App\Models\Calendar;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Nette\Schema\Expect;

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
      if($experts->isEmpty()){
        return response()->json([
            'success' => false,
            'message' => 'Experts not found',
            'data'=> null,
        ], 404);
      };
      return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => $experts
        ],200);
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
        $expertDetail = ExpertDetail::where('user_id', $id)->first();
        if (!$expertDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Expert not found!',
            ], 404);
        }
        // Bước 2: Truy cập thông tin của user thông qua mối quan hệ
        $user = $expertDetail->user;
        // Step 3: Get all calendars that are booked and available in the present and future
        $currentDateTime = date("Y-m-d H:i:s");
        $calendars = Calendar::where('expert_id', $id)
            ->where('start_time', '>=', $currentDateTime)
            ->get();
        //  suggest experts by average_rating
        $suggestExperts = ExpertDetail::where('average_rating', 'like', '%' . $expertDetail->average_rating . '%')->get();
        // Kết hợp thông tin từ $user và $expertDetail vào một mảng
        $data = [
            'expertDetail' => $expertDetail,
            'schedules' => $calendars,
            'suggestExperts' => $suggestExperts,
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

        if($experts->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'Experts not found',
                'data'=> null,
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
            'last_page_url' =>null,
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => "",
            'from' => 1,
            'to' => 10,
            'data' => [
                $experts
            ],
        ],200);
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

        if(empty($expert)){
            return response()->json([
                'success' => false,
                'message' => 'ExpertID not found',
                'data'=> null,
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
        $expertInfor = $this->getUser($request);
        $expertID = $expertInfor->id;
        $expert = $this->experts->getExpertProfile($expertID);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string', 
            'email' => 'required|string|email',
            'password' => [
                'required',
                'string',
                'min:8', 
                'regex:/[A-Z]/', // Ít nhất một chữ cái viết hoa
                'regex:/[a-z]/', // Ít nhất một chữ cái viết thường
                'regex:/[0-9]/', // Ít nhất một ký tự số
                'regex:/[!@#$%^&*()\-_=+{};:,<.>]/', // Ít nhất một ký tự đặc biệt
            ],
            'profile_picture' => 'string',
            'date_of_birth' => 'date',
            'phone_number' => [
                'numeric',
                'digits:10', // Đảm bảo số điện thoại có 10 chữ số
                'regex:/^(0)[0-9]{9}$/', // Đảm bảo số điện thoại bắt đầu bằng số 0 và theo sau là 9 chữ số
            ],
            'gender' => 'string',
            'experience' => 'string',
            'certificate' => 'string'
        ]);

        if(empty($expert)){
            return response()->json([
                'success' => false,
                'message' => 'expert ID not found',
            ], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400); //Bad request
        }

         // Kiểm tra xem email đã tồn tại cho một người dùng khác chưa
         $existingExpert = User::where('email', $request->input('email'))->where('id', '!=', $expertID)->first();
         if ($existingExpert) {
             return response()->json([
                 'success' => false,
                 'message' => 'Email already exists in the system.',
             ], 400); //Bad request
         }

        $expert->name = $request->input('name');
        $expert->email = $request->input('email');
        $expert->password = $request->input('password');
        $expert->address = $request->input('');
        $expert->phone_number = $request->input('phone_number');
        $expert->gender = $request->input('gender');
        $expert->date_of_birth = $request->input('date_of_birth');
        $expert->status = 1;
        $expert->experience = $request->input('experience');
        $expert->certificate = $request->input('certificate');
        $expert->save();
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
}
