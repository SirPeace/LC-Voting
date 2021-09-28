<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statusNames = Status::all()->map?->name;

        $count = 0;
        do {
            if (++$count === 100) {
                throw new \Exception("Can't create unique status");
            }

            $name = $this->faker->randomElement([
                'open',
                'considering',
                'closed',
                'in_progress',
                'implemented',
            ]);
        } while ($statusNames->contains($name));

        $alias = ucwords(str_replace($name, '_', ' '));

        return [
            'alias' => $alias,
            'name' => $name,
        ];
    }
}
