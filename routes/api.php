<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpertDetailController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentsPostController;
use App\Http\Controllers\FeedbackController;
use App\Models\Feedback;

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

Route::prefix('admin')->middleware('role.admin')->group(function () {
    // users
    // Tạo người dùng mới.
    Route::post('/users', [UserController::class, 'create'])->name('admin.users.create');
    // Lấy danh sách người dùng.
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    // Cập nhật thông tin người dùng.
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    // Xóa người dùng.
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.delete');

    //  Expert
    //  tạo mới chuyên gia
    Route::post('/experts', [UserController::class, 'create'])->name('admin.expert.create');
    // Lấy danh sách chuyên gia.
    Route::get('/experts', [ExpertDetailController::class, 'index']);
    // Cập nhật thông tin chuyên gia.
    Route::put('/experts/{id}', [UserController::class, 'update'])->name('admin.expert.update');
    // Xóa chuyên gia.
    Route::delete('/experts/{id}', [UserController::class, 'destroy'])->name('admin.expert.delete');

    // Post
    Route::apiResource('posts', PostController::class);
    Route::put('/posts/{id}', [PostController::class, 'updatePostStatus'])->name('admin.post.update.status');

    // comments
    // Lấy danh sáchbình luận
    Route::get('/comments', [CommentsPostController::class, 'index']);
    // Tạo bình luận mới.
    Route::post('/comments', [CommentsPostController::class, 'createPostByAdmin']);
    // update status comment
    Route::put('/comments/{commentId}', [CommentsPostController::class, 'updatePostByAdmin']);
    // delete comment
    Route::delete('/comments/{commentId}', [CommentsPostController::class, 'destroyPostByAdmin']);

    //booking
    // Lấy danh sách booking
    Route::get('/bookings', [BookingController::class, 'getAllBookings']);
    // lấy thông tin chi tiết booking
    Route::get('/bookings/{id}', [BookingController::class, 'getOneBooking']);

    // contact
    // Lấy dânh dách contact
    Route::get('/contacts', [ContactController::class, 'getAllContacts']);
    //  Lấy thông tin chi tiết contacct
    Route::get('/contacts/{id}', [ContactController::class, 'getContactDetail']);
    //  Gửi mail phản hồi contact
    Route::post('/replyEmail', [ContactController::class, 'replyEmail']);
    //  Cập nhật trạng thái contact
    Route::put('/contacts/{id}', [ContactController::class, 'updateContactStatus']);
    //  Xóa contact
    Route::delete('/contacts/{id}', [ContactController::class, 'deleteContact']);

    //  Lấy thông tin profile admin
    Route::get('/admin-profile/{id}', [UserController::class, 'showAdminProfile'])->name('admin.profile');
});

// feedbacks
Route::get('/feedbacks', [FeedbackController::class, 'getAllFeedbacks']);
//  create a new feedback
Route::post('/feedbacks/create', [FeedbackController::class, 'createFeedbackExpert']);

// user routes
Route::prefix('user')->group(function () {
    Route::get('/profile/{id}', [UserController::class, 'show'])->name('user.profile');
    Route::patch('/profile', [UserController::class, 'updateUserProfile'])->name('update.user.profile');
    Route::post('/book-calendar/{calendar_id}', [BookingController::class, 'bookCalendar'])->name('user.book.calendar');
});

// expert routes
Route::prefix('experts')->group(function () {
    Route::get('/', [ExpertDetailController::class, 'getListExpert']);
    Route::get('/profile/{id}', [ExpertDetailController::class, 'show'])->name('expert.profile');
    Route::patch('/profile', [ExpertDetailController::class, 'updateExpertProfile'])->name('update.expert.profile');
    Route::get('/{id}', [ExpertDetailController::class, 'getExpertDetail']);
    //calendar
    Route::post('/calendar', [CalendarController::class, 'createNewCalendar']);
    //  update contact
    Route::put('/calendar/{id}', [CalendarController::class, 'update']);
    //  delete contact
    Route::delete('/calendar/{id}', [CalendarController::class, 'delete']);
    // search experts
    Route::post('/search', [ExpertDetailController::class, 'search']);
    // Filter expert
    Route::post('/filter', [ExpertDetailController::class, 'filter']);
});

// post
Route::prefix('posts')->group(function () {
    // post
    // create a new post
    Route::post('/create', [PostController::class, 'store']);
    Route::delete('/delete/{id}', [PostController::class, 'destroy']);
    // comment of the post
    // create a new comment
    Route::post('/{postId}/comments/create', [CommentsPostController::class, 'store']);
    // update comment
    Route::post('/{postId}/comments/update/{commentId}', [CommentsPostController::class, 'update']);
    // delete comment
    Route::delete('/{postId}/comments/delete/{commentId}', [CommentsPostController::class, 'destroy']);
});

//contact us
// create new contact
Route::post('/contactUs', [ContactController::class, 'contactUs']);


// auth api
require __DIR__ . '/auth.php';

// csrf token
Route::get('/csrf-token', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'csrf_token' => $_COOKIE['XSRF-TOKEN'],
    ]);});
