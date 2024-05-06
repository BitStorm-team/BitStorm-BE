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
     * Handle an incoming authentication request.
     */


    //  public function store(LoginRequest $request)
    //  {
    //      $request->authenticate();
    //      $request->session()->regenerate();

    //      $user = $request->user(); // Lấy thông tin người dùng đã đăng nhập

    //      return response()->json([
    //          'user' => $user,
    //          'message' => 'Login successful'
    //      ]);
    //  }


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
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 401);
        }

        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
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
                'message' => 'Đăng nhập thành công!',
                'data' => $user,
                'access_token' => $token,
            ], 200);
        }

        // Authentication failed, return error response
        return response()->json([
            'message' => 'Thông tin đăng nhập không chính xác.'
        ], 401);
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
