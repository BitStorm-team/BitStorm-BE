<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentsPostController;
use App\Http\Controllers\ExpertDetailController;

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
// admin routes
Route::get('/experts', [ExpertDetailController::class, 'getListExpert']);
Route::prefix('admin')->group(function () {
    Route::get('/comments', [CommentsPostController::class, 'index']);

    Route::get('/expertdetail', [ExpertDetailController::class, 'index']);
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    // contact
    Route::get('/contacts', [ContactController::class, 'getAllContacts']);
    Route::get('/contacts/{id}',[ContactController::class,'getContactDetail']);
    Route::post('/reply-email', [ContactController::class, 'replyEmail']);
    Route::post('/contacts',[ContactController::class,'updateContactStatus']);
    Route::delete('/contacts/{id}',[ContactController::class,'deleteContact']);
    //post
    Route::apiResource('posts',PostController::class);
    Route::put('posts/update-status/{id}',[PostController::class,'updatePostStatus'])->name('admin.post.update.status');
});

Route::prefix('user')->group(function (){
    Route::get('/user-profile/{id}', [UserController::class, 'show'])->name('user.profile');

});

Route::prefix('expert')->group(function (){
    Route::get('/expert-profile/{id}', [ExpertDetailController::class, 'show'])->name('expert.profile');

    Route::get('/{id}', [ExpertDetailController::class, 'getExpertDetail']);
});
// auth api
require __DIR__.'/auth.php';


