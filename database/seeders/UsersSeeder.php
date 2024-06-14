<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     public static function run()
        {
            $data = [
            [
                'role_id' => 1,
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin@gmail.com'),
                'address' => '123 Main St',
                'profile_picture' => 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-photo-men-doctor-physician-chest-smiling-png-image_13143575.png',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '1234567890',
                'gender' => 'male',
                'status' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 3,
                'name' => "vanthu",
                'email' => "vanthu@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('vanthu@gmail.com'),
                'address' => '123 Main St',
                'profile_picture' => 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-photo-men-doctor-physician-chest-smiling-png-image_13143575.png',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '0958494003',
                'gender' => 'female',
                'status' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
            ];
        DB::table('users')->insert($data);
        User::factory()->count(20)->create();
        User::factory()->expert()->count(40)->create();
    }
    }
