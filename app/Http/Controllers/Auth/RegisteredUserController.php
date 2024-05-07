<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register to the website",
     *     tags={"authent"},
     *     description="Register to the website by providing email address, password, name, confirmation password, and role ID.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "confirmation_password", "role_id"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="bao@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="bao@gmail.com"),
     *             @OA\Property(property="confirmation_password", type="string", format="password", example="bao@gmail.com"),
     *             @OA\Property(property="role_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Registration successful"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', Rules\Password::defaults()],

            ]);
        } catch (ValidationException $e) {
            // tra về json khi không thành công
            return response()->json(['error' => $e->validator->errors()->first()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => '',
            'profile_picture' => asset('assets/img/avatar/avatar-4.png'),
            'date_of_birth' => null,
            'phone_number' => '',
            'gender' => '',
            'role_id' => $request->role_id,
        ]);

        event(new Registered($user));

        Auth::login($user);
        $user->role;
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Register Successfully',
            'user' =>  Auth::user()
        ]);
    }
}
