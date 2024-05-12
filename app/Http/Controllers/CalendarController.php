<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Calendar;

class CalendarController extends Controller
{

    public function createNewCalendar(Request $request){
        $validator = Validator::make($request->all(), [
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'price' => 'required|numeric|min:0',
            'describe' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $user = Auth::user();
        if($user){
            if($user->role == 3){
                $calendar = new Calendar();
                $calendar->expert_id = $user->id;
                $calendar->start_time = $request->input('start_time');
                $calendar->end_time = $request->input('end_time');
                $calendar->price = $request->input('price');
                $calendar->describe = $request->input('describe');
                $calendar->status = $request->input('status');
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
                'message' => 'Please log in to create a new calendar',
            ], 401);
        }
    }

}
