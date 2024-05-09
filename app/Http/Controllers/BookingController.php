<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Auth;
class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/bookings",
     *     summary="Display all bookings from database",
     *      tags={"Show Bookings"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getAllBookings(){
        $bookings = Booking::with('user','calendar.expertDetail.user')->paginate(10);
        if(!empty($bookings)){
            return response()->json([
                'success' => true,
                'message' => 'Show all bookings successfully',
                'data' => $bookings,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Bookings not found',
                'data'=> null,
            ], 404);
        }
    }

     /**
    * @OA\Post(
    *     path="/api/user/booking/{user_id}/book-calendar/{calendar_id}",
    *     summary="Book calendar",
    *     tags={"Book calendar"},
    *     @OA\Parameter(
    *              name="user_id",
    *              in="path",
    *              description="User ID",
    *              required=true,
    *              @OA\Schema(type="integer")
    *      ),
    *     @OA\Parameter(
    *              name="calendar_id",
    *              in="path",
    *              description="Calendar ID",
    *              required=true,
    *              @OA\Schema(type="integer")
    *      ),
    *     @OA\Parameter(
    *              name="note",
    *              in="query",
    *              description="Note",
    *              required=false,
    *              @OA\Schema(type="string")
    *      ),
    *     @OA\Response(response="200", description="Success", @OA\JsonContent()),
    *     @OA\Response(response="400", description="Bad request", @OA\JsonContent()),
    *     @OA\Response(response="401", description="Unauthorized", @OA\JsonContent()),
    *     @OA\Response(response="403", description="Forbidden", @OA\JsonContent()),
    *     @OA\Response(response="404", description="Not Found", @OA\JsonContent()),
    *     @OA\Response(response="422", description="Unprocessable Entity", @OA\JsonContent()),
    *     @OA\Response(response="500", description="Internal Server Error", @OA\JsonContent()),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function BookCalendar(Request $request, $userID, $calendarID){
        $calendar = Calendar::find($calendarID);
        $user = User::find($userID);
        
        $validator = Validator::make($request->all(), [
            'note' => 'String', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 422); //Unprocessable Entity
        }

        if (!$calendar) {
            return response()->json([
                'success' => false,
                'message' => 'The calendar does not exist!',
                'data' => null
            ], 404);
        }
    
        // Kiểm tra nếu lịch đã được đặt bởi người dùng khác
        if($calendar->status == 1){
            return response()->json([
                'success' => false,
                'message' => 'This calendar has already been booked by other users!',
                'data' => null
            ], 401);
        }
        
        // $user = Auth::user();
        if ($user->role_id != 2) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to book the calendar!',
                'data' => null
            ], 403);
        }
        
        $booking = new Booking();
        $booking->user_id = $userID;; 
        $booking->calendar_id = $calendarID;
        $booking->note = $request->note;
        $booking->status = 1;
        
        if ($booking->save()) {
            $calendar->status = 1;
            $calendar->save();
            return response()->json([
                'success' => true,
                'message' => 'Book schedule successfully!',
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Scheduling failed',
                'data' => null
            ], 500);
        }
    }
}
