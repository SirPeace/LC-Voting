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
        User::factory(100)->create();

        User::factory()->create([
            "name" => "Roman Khabibulin",
            "email" => "roman.khabibulin13@gmail.com",
            "password" => Hash::make("admin"),
        ]);

        (new CategorySeeder)->run();
        (new StatusSeeder)->run();
        (new IdeaSeeder)->run();
        (new VoteSeeder)->run();
    }
}
