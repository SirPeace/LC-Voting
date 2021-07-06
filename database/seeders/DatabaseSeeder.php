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
        User::factory()->create([
            "name" => "Big Brother",
            "email" => "admin@mail.com",
            "password" => Hash::make("admin"),
        ]);

        (new CategorySeeder)->run();
        (new StatusSeeder)->run();
        (new IdeaSeeder)->run();
        (new VotableSeeder)->run();
        (new CommentSeeder)->run();
    }
}
