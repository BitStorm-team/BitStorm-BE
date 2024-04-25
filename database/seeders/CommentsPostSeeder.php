<?php

namespace Database\Seeders;

use App\Models\CommentsPost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentsPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommentsPost::factory()->count(20)->create();
    }
}
