<?php

namespace App\Http\Controllers;

use App\Models\ExpertDetail;
use App\Models\Calendar;

use App\Models\User;
use Illuminate\Http\Request;
use Nette\Schema\Expect;

class ExpertDetailController extends Controller
{
    protected $experts;
    public function __construct()
    {
        $this->experts = new ExpertDetail();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/expertdetail",
     *     summary="Display all expert form database",
     *      tags={"Expert Details"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
        $expert = $this->experts->getAllExpert();
        return $expert;
    }

    // Get expert details
    /**
     * @OA\Get(
     *     path="/api/expert/{id}",
     *     summary="Get one expert detail ",
     *     tags={"Expert Details"},
     *          @OA\Parameter(
     *              name="id",
     *               in="path",
     *              description="Expert ID",
     *              required=true,
     *              @OA\Schema(type="integer")
     *          ),
     *     @OA\Response(response=200, description="success"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Not Found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getExpertDetail($id)
    {
        // Bước 1: Lấy chi tiết của chuyên gia dựa trên id
        $expertDetail = ExpertDetail::where('user_id', $id)->first();
        if (!$expertDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Expert not found!',
            ], 404);
        }
        // Bước 2: Truy cập thông tin của user thông qua mối quan hệ
        $user = $expertDetail->user;
        // Step 3: Get all calendars that are booked and available in the present and future
        $currentDateTime = date("Y-m-d H:i:s");
        $calendars = Calendar::where('expert_id', $id)
            ->where('start_time', '>=', $currentDateTime)
            ->get();
        //  suggest experts by average_rating
        $suggestExperts = ExpertDetail::where('average_rating', 'like', '%' . $expertDetail->average_rating . '%')->get();
        // Kết hợp thông tin từ $user và $expertDetail vào một mảng
        $data = [
            'expertDetail' => $expertDetail,
            'schedules' => $calendars,
            'suggestExperts' => $suggestExperts,
        ];
        // Trả về view với dữ liệu đã lấy được
        return response()->json([
            'success' => true,
            'message' => 'Get detail expert successfully!',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ExpertDetail $expertDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpertDetail $expertDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpertDetail  $expertDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpertDetail $expertDetail)
    {
        //
    }
}
