<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpertDetailController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/expert/{id}', [ExpertDetailController::class, 'getExpertDetail']);
Route::get('/bookings', [BookingController::class, 'getAllBookings']);

Route::get('/user-profile', [AuthController::class, 'userProfile']);

Route::middleware('web')->get('/api/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
});
