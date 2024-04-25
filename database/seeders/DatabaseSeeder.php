<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ExpertDetail;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\ExpertDetailsSeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $role = new RolesSeeder();
        for($i = 0; $i < 4; $i++){
            $role->run();
        }
        $this->call([
            UsersSeeder::class,
            ExpertDetailsSeeder::class,

        ]);
        // $user = new UsersSeeder();
        // $user->run();

    }
}
