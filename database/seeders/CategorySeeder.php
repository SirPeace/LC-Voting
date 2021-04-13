<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()->createMany([
            ['name' => 'Category 1'],
            ['name' => 'Category 2'],
            ['name' => 'Category 3'],
            ['name' => 'Category 4'],
        ]);
    }
}
