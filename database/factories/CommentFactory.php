<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::where('role_id', 2)->inRandomOrder()->firstOrFail();
        $post = Post::inRandomOrder()->firstOrFail();
        return [
            'user_id'=>$user->id,
            'post_id' =>$post->id,
            'content' => $this->faker->text(100),
        ];
    }
}
