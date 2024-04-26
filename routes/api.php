<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertDetailController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// auth routes
require __DIR__ . '/auth.php';

// admin routes
Route::prefix('admin')->group(function () {

    Route::get('/expertdetail', [ExpertDetailController::class, 'index']);
    Route::get('/contacts', [ContactController::class, 'getAllContacts']);
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
})->middleware('check.role.admin');


// expert routes
Route::prefix('expert')->group(function () {

    // code here
})->middleware('check.role.expert');
