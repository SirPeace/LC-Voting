<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\User;
use App\Models\IdeaComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaCommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IdeaComment::class;

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
}
