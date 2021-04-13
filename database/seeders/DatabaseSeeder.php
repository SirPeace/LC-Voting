<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        (new CategorySeeder)->run();
        (new StatusSeeder)->run();
    }
}
