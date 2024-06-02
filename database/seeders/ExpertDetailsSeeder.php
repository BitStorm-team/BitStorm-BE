<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpertDetail;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ExpertDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $expertUsers = User::where('role_id', 3)->get();
        $certificate = 'https://png.pngtree.com/thumb_back/fh260/background/20230511/pngtree-nature-background-sunset-wallpaer-with-beautiful-flower-farms-image_2592160.jpg';

        foreach ($expertUsers as $expertUser) {
            $existingDetail = ExpertDetail::where('user_id', $expertUser->id)->first();

            // Insert new record
            $workPlace = $faker->randomElement(["University of Oxford United Kingdom", "Harvard University", "The University of Chicago", "Carnegie Mellon University"]);
            $experienceYears = $faker->randomElement([3, 4, 5, 6, 7, 8, 9, 10]);
            $mentalHealthProblems = $faker->randomElement([
                "depression, anxiety, Obsessive-compulsive disorder",
                "Disruptive behaviour and dissocial disorders, Anxiety Disorders",
                "Depression, Generalised anxiety disorder, Panic disorder",
                "Post-traumatic stress disorder, Social anxiety disorder, Specific phobias"
            ]);
            $numberOfHelpedPeople = $faker->numberBetween(100, 1000);
            $experience = "I graduated from $workPlace. I have experience $experienceYears years in this field. I have helped $numberOfHelpedPeople people overcome their mental health problems such as $mentalHealthProblems";
            if ($existingDetail) {
                // Update existing record
                $existingDetail->update([
                    'experience' => $experience,
                    'certificate' => $certificate,
                    'average_rating' => $faker->randomFloat(1, 3, 5),
                ]);
            } else {

                $data = [
                    'user_id' => $expertUser->id,
                    'experience' => $experience,
                    'certificate' => $certificate,
                    'average_rating' => 5
                ];

                DB::table('expert_details')->insert($data);
            }
        }
    }
}
