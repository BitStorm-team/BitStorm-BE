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
            'start_time' => $this->faker->dateTime(), // Generates a random datetime
            'end_time' => $this->faker->dateTime(), // Generates a random datetime
            'price' => $this->faker->randomFloat(2, 10, 100),
            'describe' => $this->faker->paragraph,
            'status' => $this->faker->boolean()
            // Other fields of the Calendar model can be randomly generated in the factory
        ];
    }

}
