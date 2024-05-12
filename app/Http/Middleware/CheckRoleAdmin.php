<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleAdmin
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public static function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $user = JWTAuth::parseToken()->authenticate($token);

        if(($user->role_id != 1)){
            return response()->json(['message' => 'Your account is not accept.'], 409);
        }
        return $next($request);
    }
}
