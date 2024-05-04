<?php

namespace App\Http\Controllers;

use App\Models\ExpertDetail;
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
     *     path="/api/admin/expertdetail",
     *     summary="Display all expert form database",
     *      tags={"Expert Details"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index()
    {
      $expert = $this->experts->getAllExpert();
      if($expert->isEmpty()){
        return response()->json([
            'success' => false,
            'message' => 'Experts not found',
            'data'=> null,
        ], 404);
      };
      return response()->json([
        'success' => true,
        'message' => 'Success',
        'data' => [
            'experts' => $expert,
        ]
        ],200);
    }

    public function getListExpert()
    {
        $experts = $this->experts->getListExpert();
        
        if($experts->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'Experts not found',
                'data'=> null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Success',
            'total' => 15,
            'per_page' => 5,
            'current_page' => 1,
            'last_page' => 4,
            'first_page_url' => null,
            'last_page_url' =>null,
            'next_page_url' => null,
            'prev_page_url' => null,
            'path' => "",
            'from' => 1,
            'to' => 10,
            'data' => [
                $experts
            ],
        ],200);
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
