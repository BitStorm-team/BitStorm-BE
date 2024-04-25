<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\ExpertDetail;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpertDetail>
 */
class ExpertDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        /// Lấy tất cả các user có role_id = 3
        $expertUsers = User::where('role_id', 3)->get();

        foreach ($expertUsers as $expertUser) {
            ExpertDetail::updateOrCreate(
                ['user_id' => $expertUser->id],
                [
                    'experience' => $this->faker->paragraph,
                    'certificate' => $this->faker->sentence,
                    'average_rating' => $this->faker->randomFloat(1, 0, 5),
                ]
            );
        }

        return [];

    }
}
