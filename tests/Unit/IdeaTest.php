<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Votable;
use App\Voter;
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

    $this->voter = new Voter($this->idea);
});


it("can_check_if_specified_user_voted", function () {
    Votable::factory()->createMany([
        [
            "user_id" => $this->userOne->id,
            "votable_id" => $this->idea->id,
        ],
        [
            "user_id" => $this->userOne->id,
            "votable_id" => Idea::factory()->create(),
        ],
        [
            "user_id" => $this->userTwo->id,
            "votable_id" => Idea::factory()->create(),
        ],
    ]);

    $this->assertTrue($this->voter->isVotedBy($this->userOne));
    $this->assertFalse($this->voter->isVotedBy($this->userTwo));
});


it("can_be_voted_by_user", function () {
    $this->actingAs($this->userOne);

    $this->voter->vote($this->userOne);

    $this->assertTrue($this->voter->isVotedBy($this->userOne));
    $this->assertFalse($this->voter->isVotedBy($this->userTwo));
});


it("can_be_voted_only_once_by_same_user", function () {
    $this->actingAs($this->userOne);

    $this->voter->vote($this->userOne);
    $this->voter->vote($this->userOne);

    $this->assertTrue(
        Votable::where([
            'user_id' => $this->userOne->id,
            'votable_id' => $this->idea->id
        ])->count() === 1
    );
});
