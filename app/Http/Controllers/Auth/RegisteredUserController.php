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
        $roleName = $user->role->name;
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Đăn ký thành công!',
            'role' => $roleName,
            'user' =>  Auth::user()

        ]);
    }
}
