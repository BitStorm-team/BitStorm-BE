<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class BookingController extends Controller
{
        /**
     * @OA\Get(
     *     path="/bookings",
     *     tags={"Bookings"},
     *     summary="Get all bookings",
     *     description="Retrieve all bookings from the database.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Show all bookings successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Booking")),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bookings not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bookings not found"),
     *             @OA\Property(property="data", type="null"),
     *         ),
     *     ),
     * )
     **/
    public function getAllBookings(){
        $bookings = Booking::with('user','calendar.expertDetail.user')->get();
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
