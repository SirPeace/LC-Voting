<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    [$this->userOne, $this->userTwo] = User::factory(2)->create();

    $this->idea = Idea::factory()->create([
        "user_id" => User::factory()->create()->id,
    ]);
});


it("can_check_if_specified_user_voted", function () {
    Vote::factory()->createMany([
        [
            "user_id" => $this->userOne->id,
            "idea_id" => $this->idea->id,
        ],
        [
            "user_id" => $this->userOne->id,
            "idea_id" => Idea::factory()->create(),
        ],
        [
            "user_id" => $this->userTwo->id,
            "idea_id" => Idea::factory()->create(),
        ],
    ]);

    $this->assertTrue($this->idea->isVotedByUser($this->userOne));
    $this->assertFalse($this->idea->isVotedByUser($this->userTwo));
});


// it("can_be_voted_by_user", function () {
//     $this->userOne->voteForIdea($this->idea);

//     $this->assertTrue($this->idea->isVotedByUser($this->userOne));
//     $this->assertFalse($this->idea->isVotedByUser($this->userTwo));
// });


// it("can_be_voted_only_once_by_same_user", function () {
//     $this->userOne->voteForIdea($this->idea);
//     $this->userOne->voteForIdea($this->idea);

//     $this->assertTrue(
//         Vote::where([
//             'user_id' => $this->userOne->id,
//             'idea_id' => $this->idea->id
//         ])->count() === 1
//     );
// });
