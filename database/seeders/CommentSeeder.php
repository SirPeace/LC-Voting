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
        foreach (Idea::all() as $idea) {
            Comment::factory(3)->create(["idea_id" => $idea->id]);
        }
    }
}
