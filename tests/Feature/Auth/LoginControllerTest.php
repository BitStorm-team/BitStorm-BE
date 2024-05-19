<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginSuccess()
    {
        // Create a user to test authentication
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin@gmail.com'),
        ]);

        // Mock the JWT token
        $token = 'some-jwt-token';
        $tokenTTL = 60;

        // Mock the Auth attempt method to return true, indicating successful authentication
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'admin@gmail.com',
                'password' => 'admin@gmail.com',
            ])
            ->andReturn(true);

        // Mock the Auth guard to return the user when authenticated
        Auth::shouldReceive('user')->andReturn($user);

        // Mock userResolver method
        Auth::shouldReceive('userResolver')->andReturn(function () use ($user) {
            return $user;
        });

        // Mock auth()->factory()->getTTL()
        Auth::shouldReceive('factory')->andReturnSelf();
        Auth::shouldReceive('getTTL')->andReturn($tokenTTL);

        // Mock the JWT token creation
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('login')->andReturn($token);

        // Make the post request to the login endpoint
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'admin@gmail.com',
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJson([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $tokenTTL * 60,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'address' => $user->address,
                    'role_id' => $user->role_id,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                    'name' => $user->name,
                ],
            ]);
    }
}
