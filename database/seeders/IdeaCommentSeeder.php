<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\IdeaComment;
use Illuminate\Database\Seeder;

class IdeaCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Idea::all() as $idea) {
            IdeaComment::factory(3)->create(["idea_id" => $idea->id]);
        }
    }
}
