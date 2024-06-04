<?php

use App\Models\ExpertDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpertDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExpertDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Logic từ hàm generateExperience()
        $workPlace = $this->faker->randomElement(["University of Oxford United Kingdom", "Harvard University", "The University of Chicago", "Carnegie Mellon University"]);
        $experienceYears = $this->faker->randomElement([3, 4, 5, 6, 7, 8, 9, 10]);
        $mentalHealthProblems = $this->faker->randomElement([
            "depression, anxiety, Obsessive-compulsive disorder",
            "Disruptive behaviour and dissocial disorders, Anxiety Disorders",
            "Depression, Generalised anxiety disorder, Panic disorder",
            "Post-traumatic stress disorder, Social anxiety disorder, Specific phobias"
        ]);
        $numberOfHelpedPeople = $this->faker->numberBetween(100, 1000);

        // Return sentence
        return [
            'experience' => "I graduated from $workPlace. I have experience $experienceYears years in this field. I have helped $numberOfHelpedPeople people overcome their mental health problems such as $mentalHealthProblems",
            'certificate' => $this->faker->imageUrl(640, 480),
            'average_rating' => $this->faker->randomFloat(1, 0, 5),
        ];
    }
}

?>