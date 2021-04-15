<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCount = User::count();
        $ideasCount = Idea::count();

        // Make every user vote for a random idea
        foreach (range(1, $usersCount) as $user_id) {
            Vote::factory()->create([
                'user_id' => $user_id,
                'idea_id' => mt_rand(1, $ideasCount),
            ]);
        }
    }
}
