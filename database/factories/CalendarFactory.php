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

        return [
            'expert_id' => $user->id,
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'describe' => $this->faker->paragraph,
            // Các trường khác của Calendar có thể được tạo ngẫu nhiên trong factory
        ];
    }
}
