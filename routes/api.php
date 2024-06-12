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
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedbackController;
use App\Models\Feedback;
use App\Http\Controllers\PaymentController;

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
    Route::put('/posts/update/{id}', [PostController::class, 'updatePostStatus'])->name('admin.post.update.status');
    Route::post('/posts', [PostController::class, 'createPost'])->name('admin.post.create');
    // comments
    // Lấy danh sáchbình luận
    Route::get('/comments', [CommentController::class, 'index']);
    // Tạo bình luận mới.
    Route::post('/comments', [CommentController::class, 'createPostByAdmin']);
    // update status comment
    Route::put('/comments/{commentId}', [CommentController::class, 'updatePostByAdmin']);
    // delete comment
    Route::delete('/comments/{commentId}', [CommentController::class, 'destroyPostByAdmin']);

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
    //booking
    Route::get('/{userId}/bookings', [BookingController::class, 'getAllBookingsByUserId']);
    Route::get('/{userId}/bookings/{bookingId}', [BookingController::class, 'getBookingByUserIdAndBookingId']);
});

// expert routes
Route::prefix('experts')->group(function () {
    Route::get('/', [ExpertDetailController::class, 'getListExpert']);
    Route::get('/profile/{id}', [ExpertDetailController::class, 'show'])->name('expert.profile');
    Route::patch('/profile', [ExpertDetailController::class, 'updateExpertProfile'])->name('update.expert.profile');
    Route::get('/{id}', [ExpertDetailController::class, 'getExpertDetail']);
    //calendar
    Route::get('/{expertId}/calendars', [CalendarController::class, 'getCalendarsByExpertId']);
    Route::get('/{expertId}/calendars/{id}', [CalendarController::class, 'getCalendarByIdAndExpertId']);
    Route::post('/calendar', [CalendarController::class, 'createNewCalendar']);
    //  update contact
    Route::put('/calendar/{id}', [CalendarController::class, 'update']);
    //  delete contact
    Route::delete('/calendar/{id}', [CalendarController::class, 'delete']);
    // search experts
    Route::post('/search', [ExpertDetailController::class, 'search']);
    // Filter expert
    Route::post('/filter', [ExpertDetailController::class, 'filter']);
    //boooking
    Route::get('/{expertId}/bookings', [BookingController::class, 'getAllBookingsByExpertId']);
    Route::get('/{expertId}/bookings/{bookingId}', [BookingController::class, 'getBookingByExpertIdAndBookingId']);
});

// post
Route::prefix('posts')->group(function () {
    // post
    // create a new post
    Route::post('/create', [PostController::class, 'store']);
    Route::put('/update/{id}',[PostController::class,'updatePostContent']);
    Route::delete('/delete/{id}', [PostController::class, 'deletePost']);
    Route::get('/{postId}', [PostController::class, 'show']);
    Route::get('/', [PostController::class, 'index']);
    // comment of the post
    // create a new comment
    Route::post('/{postId}/comments/create', [CommentController::class, 'store']);
    // update comment
    Route::post('/{postId}/comments/update/{commentId}', [CommentController::class, 'update']);
    // delete comment
    Route::delete('/{postId}/comments/delete/{commentId}', [CommentController::class, 'destroy']);
});

//contact us
// create new contact
Route::post('/contactUs', [ContactController::class, 'contactUs']);

//VN pay
Route::post('/payment', [PaymentController::class, 'makePayment']);

//notification
// getAllPostsByUserId
Route::get('/profile/notifications',[PostController::class, 'getAllPostsByUserId']);
// auth api
require __DIR__ . '/auth.php';

