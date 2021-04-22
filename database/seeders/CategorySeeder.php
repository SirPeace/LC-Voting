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
            [
                'name' => 'category_1',
                'alias' => 'Category 1',
            ],
            [
                'name' => 'category_2',
                'alias' => 'Category 2',
            ],
            [
                'name' => 'category_3',
                'alias' => 'Category 3',
            ],
            [
                'name' => 'category_4',
                'alias' => 'Category 4',
            ],
        ]);
    }
}
