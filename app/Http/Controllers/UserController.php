<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
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
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $user = $this->user::where('role_id','=',2)->find($id);
        if(empty($user)){
            return response()->json([
                'success' => false,
                'message' => 'user ID not found',
                'data'=> null,
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
        if(empty($user)){
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
            'phone_number' => [
                'numeric',
                'digits:10', // Đảm bảo số điện thoại có 10 chữ số
                'regex:/^(0)[0-9]{9}$/', // Đảm bảo số điện thoại bắt đầu bằng số 0 và theo sau là 9 chữ số
            ],
            'gender' => 'string'
        ]);

        if(empty($user)){
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
        $user->password = $request->input('password');
        $user->address = $request->input('address ');
        $user->phone_number = $request->input('phone_number');
        $user->gender = $request->input('gender');
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
        //
    }
}
