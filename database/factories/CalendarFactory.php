<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ExpertDetail;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendar>
 */
class CalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::where('role_id', 3)->inRandomOrder()->firstOrFail();
        
        // Tạo thời gian bắt đầu từ 9 AM đến 6 PM
        $startTime = $this->faker->dateTimeBetween('2024-08-01 09:00:00', '2024-08-01 18:00:00');
        
        // Tạo thời gian kết thúc từ thời gian bắt đầu đến 2 giờ sau, là số chẵn
        $endTime = clone $startTime;
        $endTime->modify('+2 hours');
        if ($endTime->format('i') % 2 != 0) {
            // Nếu phút kết thúc là số lẻ, thêm 1 phút để biến nó thành số chẵn
            $endTime->modify('+1 minute');
        }
        
        return [
            'expert_id' => $user->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'describe' => $this->faker->paragraph,
            'status' => $this->faker->boolean()
        ];
    }
    

}
