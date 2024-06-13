<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\ExpertDetail;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = new User();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     summary="Display all users",
     *     tags={"List users"},
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        //
        $users = User::all();
        return response()->json([
            "success" => true,
            "message" => "Get all users successfully",
            "data" => $users
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Kiểm tra thông tin người dùng cho các vai trò khác
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'experience' => 'nullable|string', // Trường experience có thể null
            'certificate' => 'nullable|string', // Trường certificate có thể null
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => '',
            'profile_picture' => asset('assets/img/avatar/avatar-4.png'),
            'date_of_birth' => null,
            'phone_number' => '',
            'gender' => '',
            'status' => true,
            'role_id' => $request->role_id,
        ]);

        // Nếu vai trò là người dùng chuyên gia (role_id = 3) và có trường experience và certificate được cung cấp
        if ($request->role_id == 3 && $request->has('experience') && $request->has('certificate')) {
            // Kiểm tra xem đã có dữ liệu trong bảng expert_details tương ứng với user_id của người dùng mới hay không
            $checkExpert = DB::table('expert_details')->where('user_id', $user->id)->exists();

            // Nếu có dữ liệu, xóa dữ liệu cũ trước khi tạo mới
            if ($checkExpert) {
                DB::table('expert_details')->where('user_id', $user->id)->delete();
            }
            // Tạo chi tiết chuyên gia mới
            ExpertDetail::create([
                'user_id' => $user->id,
                'certificate' => $request->certificate,
                'experience' => $request->experience,
                'count_rating' => 5
            ]);
        }

        return response()->json([
            'message' => 'Created successfully!',
            'user' => $user
        ], 201);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/user/user-profile/{id}",
     *     summary="Display user profile",
     *     tags={"User profile"},
     *     @OA\Parameter(
     *              name="id",
     *              in="path",
     *              description="User ID",
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
        //
        $user = $this->user::where('role_id', '=', 2)->find($id);
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'user ID not found',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Show profile user successfully!',
            'data' => $user,
        ], 200);
    }
    public function showAdminProfile($id)
    {
        $user = $this->user::where('role_id', '=', 1)->find($id);
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'user ID not found',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Show profile user successfully!',
            'data' => $user,
        ], 200);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Put(
     *     path="/api/admin/users/{id}",
     *     summary="Update user status",
     *     tags={"Update user status"},
     *     @OA\Parameter(
     *              name="id",
     *              in="path",
     *              description="User ID",
     *              required=true,
     *              @OA\Schema(type="integer")
     *      ),
     *     @OA\Parameter(
     *              name="status",
     *              in="query",
     *              description="Status of the user",
     *              required=true,
     *              @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'user ID not found',
            ], 404);
        }
        $user->status = $request->input('status');
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => $user,
        ], 200);
    }

    public function updateUserProfile(Request $request)
    {
        $userInfor = $this->getUser($request);
        $userID = $userInfor->id;
        $user = User::find($userID);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'profile_picture' => 'string|url',
            'phone_number' => [
                'numeric',
                'digits:10', // Đảm bảo số điện thoại có 10 chữ số
                'regex:/^(0)[0-9]{9}$/', // Đảm bảo số điện thoại bắt đầu bằng số 0 và theo sau là 9 chữ số
            ],
            'gender' => 'string'
        ]);

        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'user ID not found',
            ], 404);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400); //Bad request
        }

        // Kiểm tra xem email đã tồn tại cho một người dùng khác chưa
        $existingUser = User::where('email', $request->input('email'))->where('id', '!=', $userID)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists in the system.',
            ], 400); //Bad request
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->address = $request->input('address ');
        $user->phone_number = $request->input('phone_number');
        $user->gender = $request->input('gender');
        $user->profile_picture = $request->input('profile_picture');
        $user->status = 1;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User ID not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }

    // get user statistics by created_at month
    public function userStatsByCreatedAt()
    {
        // Lấy dữ liệu thống kê số lượng người dùng theo tháng dựa trên created_at
        $userStats = User::select(
            DB::raw('SUBSTRING(DATE_FORMAT(created_at, "%M"), 1, 3) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Trả về dữ liệu thống kê
        return response()->json([
            'success' => true,
            'message' => 'User statistics fetched successfully',
            'data' => $userStats,
        ], 200);
    }

    //
    public function getMonthlyBookingStats()
    {
        // Lấy dữ liệu thống kê số lượng người dùng theo tháng dựa trên created_at
        $userStats = Booking::select(
            DB::raw('SUBSTRING(DATE_FORMAT(created_at, "%M"), 1, 3) as month'),
            DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Trả về dữ liệu thống kê
        return response()->json([
            'success' => true,
            'message' => 'User statistics fetched successfully',
            'data' => $userStats,
        ], 200);
    }

    public function getAllCalendar(){
        $calendars = DB::table('calendars')->get();
         // Trả về dữ liệu thống kê
         return response()->json([
            'success' => true,
            'message' => 'User statistics fetched successfully',
            'data' => $calendars,
        ], 200);
    }

}
