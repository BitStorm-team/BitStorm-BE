<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;



class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Log in to the web application
     *
     * @OA\Post(
     *      path="/api/auth/login",
     *      tags={"Auth"},
     *      summary="Login into the web application",
     *      description="Log in to the web application",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", example="bao12@gmail.com", description="Email address"),
     *              @OA\Property(property="password", type="string", example="bao12@gmail.com", description="Password to login"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully logged in",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="Indicates whether the request was successful"),
     *              @OA\Property(property="message", type="string", example="Logged in successfully!", description="A message describing the outcome of the request"),
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
     *          description="Internal server error. Failed to log in."
     *      ),
     *      @OA\Parameter(
     *          name="X-CSRF-TOKEN",
     *          in="header",
     *          description="CSRF token for authentication",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              example="eyJpdiI6IkFqTEs5enhxUk9vbWVJWVBoMXpPMFE9PSIsInZhbHVlIjoiaEhmL3ZCdXRpVDlYbGlFUFQwL1NJTlMzb1lSdE9UOFNSc0RNZnJ6ZElYbFVseXo5T2lYZ1p0N01MZEh3Nm5MNHl1U1B4NXZObzRzSGd1aUMwSG5PQVorRXAwY1NwTHkwb0RXdWJmQkdBQkwrMkM1Wi9NTjRuL3lZejVoaHllR0UiLCJtYWMiOiIzY2MzNjQ2OGJiMzBlODI4MzY3NWJmODMwZGJkOGFiYzViYTkxM2FkMGE2MzlhYzJhODVmNjllNWEwZDk2NGU1IiwidGFnIjoiIn0=",
     *          ),
     *      ),
     * )
     **/


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Thêm kiểm tra duy nhất cho email
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Register a new user
     *
     * @OA\Post(
     *      path="/api/auth/register",
     *      tags={"Auth"},
     *      summary="Register a new user",
     *      description="Register a new user with the provided information",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "password_confirmation", "role_id"},
     *              @OA\Property(property="name", type="string", example="John Doe", description="User's name"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User's email address"),
     *              @OA\Property(property="password", type="string", example="password123", description="User's password (min: 6 characters)"),
     *              @OA\Property(property="password_confirmation", type="string", example="password123", description="Confirmation of the user's password"),
     *              @OA\Property(property="role_id", type="integer", example=1, description="ID of the user's role"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User successfully registered",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User successfully registered"),
     *
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
     *          response=422,
     *          description="Unprocessable Entity. Validation errors occurred.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}}),
     *          )
     *      ),
     *      security={{"bearerAuth": {}}}
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email', // Thêm kiểm tra duy nhất cho email
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id', // Kiểm tra role_id tồn tại trong bảng roles
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Tạo người dùng mới với các trường được chỉ định
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => '', // Hoặc bạn có thể thêm giá trị mặc định khác nếu cần thiết
            'profile_picture' => asset('assets/img/avatar/avatar-4.png'),
            'date_of_birth' => null,
            'phone_number' => '',
            'gender' => '',
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }



    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Log out the authenticated user
     *
     * @OA\Post(
     *      path="/api/auth/logout",
     *      tags={"Auth"},
     *      summary="Log out the authenticated user",
     *      description="Log out the currently authenticated user",
     *      @OA\Response(
     *          response=200,
     *          description="User successfully signed out",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User successfully signed out"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized. Authentication is required."
     *      ),
     *      security={{"bearerAuth": {}}}
     * )
     */

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh()); // using ok
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, // Thời gian sống của token tính theo giây
            'user' => auth()->user() // using ok
        ]);
    }


    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(

            ['password' => bcrypt($request->new_password)]
        );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }
}
