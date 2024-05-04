<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::where('role_id', 2)->inRandomOrder()->firstOrFail();
        $calendar = Calendar::inRandomOrder()->firstOrFail();
        return [
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
            'note' => $this->faker->text(),
            'status' => $this->faker->boolean(),
        ];
    }
}
