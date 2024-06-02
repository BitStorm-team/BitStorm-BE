<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\FeedbackExpert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FeedbackExpertFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FeedbackExpert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'booking_id' => $this->faker->numberBetween(1, 50), // Generates a random number between 1 and 100
            'rating' => $this->faker->numberBetween(1, 5), // Generates a random number between 1 and 5
            'content' => $this->faker->sentence(), // Generates a random sentence
        ];
    }
}
