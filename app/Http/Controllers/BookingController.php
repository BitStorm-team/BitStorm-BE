<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;
use App\Models\Calendar;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/bookings",
     *     summary="Display all bookings from the database",
     *     tags={"Show Bookings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         description="Bearer token for authentication",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="Bearer YOUR_TOKEN_HERE"
     *         )
     *     ),
     * @OA\SecurityScheme(
     *         securityScheme="X-CSRF-TOKEN",
     *         type="apiKey",
     *         in="header",
     *         name="X-CSRF-TOKEN",
     *         description="CSRF Token"
     *     )
     * )
     */


    public function getAllBookings()
    {
        $bookings = Booking::with('user', 'calendar.expertDetail.user')->get();
        if (!empty($bookings)) {
            return response()->json([
                'success' => true,
                'message' => 'Show all bookings successfully',
                'data' => $bookings,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Bookings not found',
                'data' => null,
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
     *     @OA\Response(response="404", description="Not Found", @OA\JsonContent()),
     *     @OA\Response(response="417", description="Not Found", @OA\JsonContent()),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function bookCalendar(Request $request, $calendarID)
    {
        $calendar = Calendar::find($calendarID);

        $user = $this->getUser($request);
        $userID = $user->id;
        $validator = Validator::make($request->all(), [
            'note' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400); //Bad request
        }

        if (!$calendar) {
            return response()->json([
                'message' => 'The calendar does not exist!',
            ], 404);
        }

        // Kiểm tra nếu lịch đã được đặt bởi người dùng khác
        if ($calendar->status == 0) {
            return response()->json([
                'message' => 'This calendar has already been booked by other users!',
            ], 409);
        }

        // $user = Auth::user();
        if ($user->role_id != 2) {
            return response()->json([
                'message' => 'You are not authorized to book the calendar!',
            ], 401);
        }

        $booking = new Booking();
        $booking->user_id = $userID;
        $booking->calendar_id = $calendarID;
        $booking->note = $request->note;
        $booking->status = 'New';

        $link_rooms = ['rdf-ibfc-jia', 'ffa-bpkj-mwk', 'xse-abqk-zst', 'gid-ubfk-aze', 'iua-ipvh-gvg','ktk-meuw-zih'];

        // Select a random room
        $random_key = array_rand($link_rooms);
        $link_room = $link_rooms[$random_key];

        // Assign the randomly selected room to the booking link
        $booking->link_room = 'https://meet.google.com/' . $link_room;

        if ($booking->save()) {
            $calendar->status = 0;
            $calendar->save();
            return response()->json([
                'success' => true,
                'message' => 'Book calendar successfully!',
                'data' => $booking
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Scheduling failed',
                'data' => null
            ], 417);
        }
    }

    //  get one booking
    public function getOneBooking($id)
    {
        $bookings = Booking::with('user', 'calendar.expertDetail.user')->where('id', $id)->paginate(10);
        if (!empty($bookings)) {
            return response()->json([
                'success' => true,
                'message' => 'Show one bookings successfully',
                'data' => $bookings,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Bookings not found',
                'data' => null,
            ], 404);
        }
    }

    public function generateRandomGoogleMeetLink()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $googleMeetLink = '';
        for ($i = 0; $i < 3; $i++) {
            if ($i == 1) {
                for ($j = 0; $j < 4; $j++) {
                    $googleMeetLink .= $characters[rand(0, strlen($characters) - 1)];
                }
            } else {
                for ($j = 0; $j < 3; $j++) {
                    $googleMeetLink .= $characters[rand(0, strlen($characters) - 1)];
                }
            }
            if ($i < 2) {
                $googleMeetLink .= '-';
            }
        }
        return $googleMeetLink;
    }

    // get list user's booking
    public function getAllBookingsByExpertId(Request $request, $expertId)
    {
        $user = $this->getUser($request);

        // Kiểm tra quyền truy cập
        if ($user->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access bookings of this expert',
            ], 403);
        }

        $bookings = Booking::with('user', 'calendar.expertDetail.user')
            ->whereHas('calendar', function ($query) use ($expertId) {
                $query->where('expert_id', $expertId);
            })->get();

        return response()->json([
            'success' => true,
            'data' => [
                'data' => $bookings
            ],
        ], 200);
    }

    public function getBookingByExpertIdAndBookingId(Request $request, $expertId, $bookingId)
    {
        $user = $this->getUser($request);
        if ($user->id !== $expertId && $user->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this booking',
            ], 403);
        }

        $booking = Booking::with(['user', 'calendar.expertDetail.user'])
            ->where('id', $bookingId)
            ->whereHas('calendar', function ($query) use ($expertId) {
                $query->where('expert_id', $expertId);
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found for the specified ID and expert ID',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $booking,
        ], 200);
    }
    public function getAllBookingsByUserId(Request $request)
    {
        $user = $this->getUser($request);

        // Kiểm tra quyền truy cập
        if ($user->role_id === 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access bookings of this user',
            ], 403);
        }

        $bookings = Booking::with(['user', 'calendar.expertDetail.user'])
            ->where('user_id', $user->id)
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ], 200);
    }
    public function getBookingByUserIdAndBookingId(Request $request, $userId, $bookingId)
    {
        $user = $this->getUser($request);

        // Kiểm tra quyền truy cập
        if ($user->id !== $userId && $user->role_id !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this booking',
            ], 403);
        }

        $booking = Booking::with(['user', 'calendar.expertDetail.user'])
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found for the specified ID and user ID',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking,
        ], 200);
    }
}
