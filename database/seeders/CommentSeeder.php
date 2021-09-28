<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 3 comments for each idea
        Idea::all()->each(function (Idea $idea) {
            Comment::factory()->existing()->for($idea)->count(3)->create();
        });
    }
}
