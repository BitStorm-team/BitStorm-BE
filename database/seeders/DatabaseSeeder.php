<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsersSeeder;

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

        $user = new UsersSeeder();
        $user->run();
        $contact = new ContactSeeder();
        $contact->run();
        $this->call([
            RolesSeeder::class,
            UsersSeeder::class
        ]);

    }
}
