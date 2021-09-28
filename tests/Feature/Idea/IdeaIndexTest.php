<?php

use App\Models\Idea;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->idea = Idea::factory()->create([
        "user_id" => User::factory()->create(),
    ]);

    $this->idea->votes_count = 0;
    $this->idea->voted_by_user = 0;
});


test("index_route_displays_index_livewire_component", function () {
    $this->get(route("idea.index"))
        ->assertSeeLivewire("idea-index");
});


test("ideas_index_component_receives_votes_count_correctly", function () {
    $this->idea->voters()->attach(User::factory(2)->create()->map->id);

    Livewire::test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            $this->assertEquals($ideas->first()->id, $this->idea->id);

            return $ideas->first()->votes_count == 2;
        });
});


test("idea_index_component_receives_correct_votes_count", function () {
    $this->idea->voters()->attach(User::factory(2)->create()->map->id);

    Livewire::test("idea-show", ['idea' => $this->idea])
        ->assertSet('votesCount', 2);
});


test("user_can_see_if_the_idea_was_voted_by_him", function () {
    $loggedInUser = User::factory()->create();

    $this->idea->voters()->attach($loggedInUser->id);

    $this->idea->votes_count += 1;
    $this->idea->voted_by_user = $loggedInUser->id;

    Livewire::actingAs($loggedInUser)
        ->test("idea-index", ['idea' => $this->idea])
        ->assertSet('isVoted', true)
        ->assertSeeHtml('<div class="text-sm font-bold leading-none  text-blue ">1</div>')
        ->assertSee('Voted');
});


test("logged_in_user_can_vote", function () {
    $loggedInUser = User::factory()->create();

    $this->assertDatabaseMissing('votes', [
        'user_id' => $loggedInUser->id,
        'idea_id' => $this->idea->id,
    ]);

    Livewire::actingAs($loggedInUser)
        ->test("idea-index", ['idea' => $this->idea])
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
    Livewire::test("idea-index", ['idea' => $this->idea])
        ->call('vote')
        ->assertRedirect(route('login'));
});
