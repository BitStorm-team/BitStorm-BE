<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpertDetail;
use Faker\Provider\Lorem;
use Illuminate\Support\Facades\DB;

class ExpertDetailsSeeder extends Seeder
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
                'user_id' => 2,
                'experience' => 'Có 10 năm kinh nghiệm trong việc tư vấn',
                'certificate' => 'https://png.pngtree.com/thumb_back/fh260/background/20230511/pngtree-nature-background-sunset-wallpaer-with-beautiful-flower-farms-image_2592160.jpg',
                'average_rating' => 5
            ]
        ];
        DB::table('expert_details')->insert($data);
    }
}
