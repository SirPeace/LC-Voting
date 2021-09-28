<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'idea_id' => Idea::factory(),
            'body'    => $this->faker->paragraph()
        ];
    }

    /**
     * Use existing models for relationships.
     *
     * @return array
     */
    public function existing(): static
    {
        // Numbers are set according to seeders
        return $this->state(fn (array $attributes) => [
            'user_id' => $this->faker->numberBetween(1, 20),
            'idea_id' => $this->faker->numberBetween(1, 100),
        ]);
    }
}
