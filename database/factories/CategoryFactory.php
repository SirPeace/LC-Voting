<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statusNames = Category::all()->map?->name;

        $count = 0;
        do {
            if (++$count === 100) {
                throw new \Exception("Can't create unique category");
            }

            $name = $this->faker->randomElement([
                'category_1',
                'category_2',
                'category_3',
                'category_4',
            ]);
        } while ($statusNames->contains($name));

        $alias = ucwords(str_replace($name, '_', ' '));

        return [
            'alias' => $alias,
            'name' => $name,
        ];
    }
}
