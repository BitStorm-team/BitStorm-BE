<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Calendar;

class CalendarController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/createNewCalendar",
 *     tags={"Calendar"},
 *     summary="Create a new calendar",
 *     description="Create a new calendar for an expert",
 *     operationId="createNewCalendar",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Calendar data",
 *         @OA\JsonContent(
 *             required={"start_time", "end_time", "price", "describe"},
 *             @OA\Property(property="start_time", type="string", format="date", description="Start time of the calendar"),
 *             @OA\Property(property="end_time", type="string", format="date", description="End time of the calendar"),
 *             @OA\Property(property="price", type="number", format="float", description="Price of the calendar"),
 *             @OA\Property(property="describe", type="string", description="Description of the calendar")
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Create new calendar successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="There is already a calendar within this time range")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Please log in as an expert to create a new calendar")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Please log in as an expert to create a new calendar")
 *         )
 *     ),
 * )
 */
    public function createNewCalendar(Request $request){
        $validator = Validator::make($request->all(), [
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'price' => 'required|numeric|min:0',
            'describe' => 'required|string|min:10',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $user = $this->getUser($request);
        if($user){
            if($user->role_id == 3){
                $existingCalendar = Calendar::where('expert_id', $user->id)
                                            ->where(function($query) use ($request) {
                                                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                                                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
                                            })
                                            ->first();
                if($existingCalendar){
                    return response()->json([
                        'success' => false,
                        'message' => 'There is already a calendar within this time range',
                    ], 400);
                }
                
                $calendar = new Calendar();
                $calendar->expert_id = $user->id;
                $calendar->start_time = $request->start_time;
                $calendar->end_time = $request->end_time;
                $calendar->price = $request->price;
                $calendar->describe = $request->describe;
                $calendar->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Create new calendar successfully',
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in as an expert to create a new calendar',
                ], 403);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Please log in as an expert to create a new calendar',
            ], 401);
        }
    }      
}

