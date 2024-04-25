<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        $data = [
            ['name' => 'admin'],
            ['name' => 'customer'],
            ['name' => 'expert'],
        ];
        DB::table('roles')->insert($data);
    }
}
