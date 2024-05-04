<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calendar;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sử dụng factory để tạo 50 bản ghi cho bảng `calendars`
        \App\Models\Calendar::factory(10)->create();
    }
}
