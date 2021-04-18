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


test("index_route_displays_index_livewire_component", function () {
    $this->get(route("idea.index"))
        ->assertSeeLivewire("idea-index");
});


test("index_page_receives_votes_count_correctly", function () {
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

    $this->get(route("idea.index"))
        ->assertViewHas("ideas", function ($ideas) {
            $this->assertEquals($ideas->first()->id, $this->idea->id);

            return intval($ideas->first()->votes_count) === 2;
        });
});


test("idea_index_livewire_component_receives_correct_votes_count", function () {
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

    $response = $this->actingAs($loggedInUser)->get(route('idea.index'));
    $ideaWithVotes = $response['ideas']->first();

    Livewire::actingAs($loggedInUser)
        ->test("idea-index", ['idea' => $ideaWithVotes])
        ->assertSet('isVoted', true)
        ->assertSeeHtml('<div class="text-sm font-bold leading-none  text-blue ">1</div>')
        ->assertSee('Voted');
});


test("logged_in_user_can_vote", function () {
    $loggedInUser = User::factory()->create();

    $response = $this->actingAs($loggedInUser)->get(route('idea.index'));
    $ideaWithVotes = $response['ideas']->first();

    $this->assertDatabaseMissing('votes', [
        'idea_id' => $this->idea->id,
        'user_id' => $loggedInUser->id,
    ]);

    Livewire::actingAs($loggedInUser)
        ->test("idea-index", ['idea' => $ideaWithVotes])
        ->assertSet('isVoted', false)
        ->assertSet('votesCount', 0)
        ->assertSee('Vote')
        ->call('vote')
        ->assertSet('isVoted', true)
        ->assertSet('votesCount', 1)
        ->assertSee('Voted');

    $this->assertDatabaseHas('votes', [
        'user_id' => $loggedInUser->id,
        'idea_id' => $this->idea->id,
    ]);
});


test("guest_gets_redirected_to_login_page_when_voting", function () {
    $response = $this->get(route('idea.index'));
    $ideaWithVotes = $response['ideas']->first();

    Livewire::test("idea-index", ['idea' => $ideaWithVotes])
        ->call('vote')
        ->assertRedirect(route('login'));
});
