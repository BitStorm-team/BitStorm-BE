<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::where('role_id', 2)->inRandomOrder()->firstOrFail();
        return [
            'user_id'=>$user->id,
            'content' => $this->faker->text(500), // Nội dung ngẫu nhiên 500 ký tự
            'is_anonymous' => $this->faker->boolean(50),
            
        ];
    }
}
