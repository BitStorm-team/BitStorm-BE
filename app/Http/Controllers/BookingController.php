<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

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
}
