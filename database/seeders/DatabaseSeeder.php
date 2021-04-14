<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        (new IdeaSeeder)->run();

        User::factory()->create([
            "name" => "Roman Khabibulin",
            "email" => "roman.khabibulin13@gmail.com",
            "password" => Hash::make("admin"),
        ]);
    }
}
