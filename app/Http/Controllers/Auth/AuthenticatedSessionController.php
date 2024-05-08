<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login to the website",
     *     tags={"authent"},
     *     description="Login to the website by providing email address and password.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="admin@gmail.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function store(LoginRequest $request)
    {
        $request->authenticate();

        // Get email and password from request
        $email = $request->email;
        $password = $request->password;

        // Find user by email
        $user = User::where('email', $email)->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => 'The information to login is not available'
            ], 401);
        }

        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed, generate token
            $token = $user->createToken('AuthToken')->plainTextToken;
            Auth::login($user);
            $user->role;
            // Return success response with token and user details
            return response()->json([
                'success' => true,
                'message' => 'Login successfully!',
                'data' => $user,
                'access_token' => $token,
            ], 200);
        }

    }


    /**
     * Destroy an authenticated session.
     */




    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'User logged out successfully.'
        ], 200);
    }
}
