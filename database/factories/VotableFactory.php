<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\User;
use App\Models\Votable;
use Illuminate\Database\Eloquent\Factories\Factory;

class VotableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Votable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, User::count()),
            'votable_id' => $this->faker->numberBetween(1, Idea::count()),
            'votable_type' => array_rand([Idea::class]),
        ];
    }
}
