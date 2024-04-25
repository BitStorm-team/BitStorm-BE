<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\RepliesPost;
use App\Models\CommentsPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RepliesPost>
 */
class RepliesPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
        $user = User::where('role_id', 2)->inRandomOrder()->firstOrFail();
        $commentPost = CommentsPost::inRandomOrder()->firstOrFail();
        return [
            'user_id'=>$user->id,
            'comment_post_id' =>$commentPost->id,
            'content' => $this->faker->text(500),
        ];
    }
}
