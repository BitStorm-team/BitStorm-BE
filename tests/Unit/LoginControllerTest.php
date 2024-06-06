<?php


namespace Tests\Unit;


use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AuthController;
use Mockery;


class LoginControllerTest extends TestCase
{
    public function testLoginSuccess()
    {
        // Create a mock request
        $request = Request::create('/api/auth/login', 'POST', [
            'email' => 'admin@gmail.com',
            'password' => 'admin@gmail.com',
        ]);


        // Mock the Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        Validator::shouldReceive('validated')
            ->once()
            ->andReturn([
                'email' => 'admin@gmail.com',
                'password' => 'admin@gmail.com',
            ]);


        // Mock the Auth attempt method to return a JWT token
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'admin@gmail.com',
                'password' => 'admin@gmail.com',
            ])
            ->andReturn('some-jwt-token');


        // Mock the auth()->user() and auth()->factory()->getTTL() calls
        $user = (object) ['id' => 1, 'email' => 'admin@gmail.com'];
        Auth::shouldReceive('user')
            ->andReturn($user);
        Auth::shouldReceive('factory')
            ->andReturnSelf();
        Auth::shouldReceive('getTTL')
            ->andReturn(60);


        // Call the login method
        $controller = new AuthController();
        $response = $controller->login($request);


        // Assert the response status
        $this->assertEquals(200, $response->status());


        // Decode the JSON response
        $responseData = json_decode($response->getContent(), true);


        // Assert the JSON response structure and values
        $this->assertArrayHasKey('access_token', $responseData);
        $this->assertArrayHasKey('token_type', $responseData);
        $this->assertArrayHasKey('expires_in', $responseData);
        $this->assertArrayHasKey('user', $responseData);


        $this->assertEquals('some-jwt-token', $responseData['access_token']);
        $this->assertEquals('bearer', $responseData['token_type']);
        $this->assertEquals(3600, $responseData['expires_in']);
        $this->assertEquals(1, $responseData['user']['id']);
        $this->assertEquals('admin@gmail.com', $responseData['user']['email']);
    }


    public function testLoginValidationFails()
    {
        // Create a mock request
        $request = Request::create('/api/auth/login', 'POST', [
            'email' => 'admin@gmail.com',
            'password' => '',
        ]);


        // Mock the Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(true);
        Validator::shouldReceive('errors')
            ->once()
            ->andReturn(['password' => ['The password field is required.']]);


        // Call the login method
        $controller = new AuthController();
        $response = $controller->login($request);


        // Assert the response status
        $this->assertEquals(422, $response->status());


        // Decode the JSON response
        $responseData = json_decode($response->getContent(), true);


        // Assert the JSON response structure and values
        $this->assertArrayHasKey('password', $responseData);
        $this->assertEquals(['The password field is required.'], $responseData['password']);
    }


    public function testLoginUnauthorized()
    {
        // Create a mock request
        $request = Request::create('/api/auth/login', 'POST', [
            'email' => 'admin@gmail.com',
            'password' => 'wrongpassword',
        ]);


        // Mock the Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(false);
        Validator::shouldReceive('validated')
            ->once()
            ->andReturn([
                'email' => 'admin@gmail.com',
                'password' => 'wrongpassword',
            ]);


        // Mock the Auth attempt method to return false
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'admin@gmail.com',
                'password' => 'wrongpassword',
            ])
            ->andReturn(false);


        // Call the login method
        $controller = new AuthController();
        $response = $controller->login($request);


        // Assert the response status
        $this->assertEquals(401, $response->status());


        // Decode the JSON response
        $responseData = json_decode($response->getContent(), true);


        // Assert the JSON response structure and values
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Unauthorized', $responseData['error']);
    }


    public function testLoginEmptyRequest()
    {
        // Create a mock request with empty values
        $request = Request::create('/api/auth/login', 'POST', [
            'email' => '',
            'password' => '',
        ]);


        // Mock the Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf();
        Validator::shouldReceive('fails')
            ->once()
            ->andReturn(true);
        Validator::shouldReceive('errors')
            ->once()
            ->andReturn([
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]);


        // Call the login method
        $controller = new AuthController();
        $response = $controller->login($request);


        // Assert the response status
        $this->assertEquals(422, $response->status());


        // Decode the JSON response
        $responseData = json_decode($response->getContent(), true);


        // Assert the JSON response structure and values
        $this->assertArrayHasKey('email', $responseData);
        $this->assertArrayHasKey('password', $responseData);
        $this->assertEquals(['The email field is required.'], $responseData['email']);
        $this->assertEquals(['The password field is required.'], $responseData['password']);
    }
}
