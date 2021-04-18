<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Vote;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $this->idea = Idea::factory()->create([
        "user_id" => User::factory()->create(),
    ]);
});


test("show_route_displays_show_livewire_component", function () {
    $this->get(route("idea.show", $this->idea))
        ->assertSeeLivewire("idea-show")
        ->assertViewHas("idea", $this->idea);
});


test("show_page_receives_votes_count_correctly", function () {
    Vote::factory()->createMany([
        [
            "idea_id" => $this->idea->id,
            "user_id" => User::factory()->create()->id,
        ],
        [
            "idea_id" => $this->idea->id,
            "user_id" => User::factory()->create()->id,
        ]
    ]);

    $this->get(route("idea.show", $this->idea))
        ->assertViewHas("idea", function ($idea) {
            $this->assertEquals($idea->id, $this->idea->id);

            return intval($idea->votes()->count()) === 2;
        });
});


test("idea_show_livewire_component_receives_correct_votes_count", function () {
    Vote::factory()->createMany([
        [
            "idea_id" => $this->idea->id,
            "user_id" => User::factory()->create()->id,
        ],
        [
            "idea_id" => $this->idea->id,
            "user_id" => User::factory()->create()->id,
        ]
    ]);

    Livewire::test("idea-show", ['idea' => $this->idea])
        ->assertSet('votesCount', 2);
});


test("user_can_see_if_the_idea_was_voted_by_him", function () {
    $loggedInUser = User::factory()->create();

    Vote::factory()->createOne([
        "idea_id" => $this->idea->id,
        "user_id" => $loggedInUser->id,
    ]);

    Livewire::actingAs($loggedInUser)
        ->test("idea-show", ['idea' => $this->idea])
        ->assertSet('isVoted', true)
        ->assertSeeHtml('<div class="text-sm font-bold leading-none  text-blue ">1</div>')
        ->assertSee('Voted');
});
