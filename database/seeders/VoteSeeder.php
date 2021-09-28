<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        // Make every user vote for 5 random ideas with no duplicates
        foreach (range(1, $usersCount) as $userId) {
            $voted = [];
            foreach (range(1, 5) as $_) {
                do {
                    $ideaId = mt_rand(1, $ideasCount);
                } while (in_array($ideaId, $voted));

                $voted[] = $ideaId;

                DB::table('votes')->insert([
                    'idea_id' => $ideaId,
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
