<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
               'booking_id' => 1,
               'rating' => 5,
               'content' => "Bác sĩ này đẹp trai quá",
            ]
        ];
        DB::table('feedback_experts')->insert($data);
    }
}
