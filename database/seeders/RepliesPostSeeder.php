<?php

namespace Database\Seeders;

use App\Models\RepliesPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RepliesPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RepliesPost::factory()->count(20)->create();
    }
}
