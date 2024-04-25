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

     public function run()
    {
        // Thêm người dùng admin
        $data = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin@gmail.com'), // Băm mật khẩu
                'address' => '123 Main St',
                'profile_picture' => 'profile.jpg',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '1234567890',
                'gender' => 'male',
                'role_id' => 1,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => "vanthu",
                'email' => "vanthu@gmail.com",
                'email_verified_at' => now(),
                'password' => Hash::make('vanthu@gmail.com'),
                'address' => '123 Main St',
                'profile_picture' => 'profile.jpg',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '0958494003',
                'gender' => 'female',
                'role_id' => 3,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
            ];
        DB::table('users')->insert($data);
    }
    }
