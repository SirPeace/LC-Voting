<?php

use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->idea = Idea::factory()->create();
});


test("show_route_displays_show_livewire_component", function () {
    $this->get(route("idea.show", $this->idea))
        ->assertSeeLivewire("idea-show")
        ->assertViewHas("idea", $this->idea);
});


test("show_page_receives_votes_count_correctly", function () {
    $this->idea->voters()->attach(User::factory(2)->create()->map->id);

    $this->get(route("idea.show", $this->idea))
        ->assertViewHas("idea", function ($idea) {
            $this->assertEquals($idea->id, $this->idea->id);

            return intval($idea->votes()->count()) === 2;
        });
});


test("idea_show_livewire_component_receives_correct_votes_count", function () {
    $this->idea->voters()->attach(User::factory(2)->create()->map->id);

    Livewire::test("idea-show", ['idea' => $this->idea])
        ->assertSet('votesCount', 2);
});


test("user_can_see_if_the_idea_was_voted_by_him", function () {
    $loggedInUser = User::factory()->create();

    $this->idea->voters()->attach($loggedInUser->id);

    Livewire::actingAs($loggedInUser)
        ->test("idea-show", ['idea' => $this->idea])
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
        ->test("idea-show", ['idea' => $this->idea])
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
    Livewire::test("idea-show", ['idea' => $this->idea])
        ->call('vote')
        ->assertRedirect(route('login'));
});


test("back_link_does_not_emit_query_string_if_index_page_was_visited_first", function () {
    $queryString = '?filter=top_voted&status=implemented';

    $this->get(route('idea.index') . $queryString);
    $response = $this->get(route('idea.show', $this->idea));

    $this->assertStringContainsString($queryString, $response['backURL']);
});


test("back_link_redirects_to_index_page_if_show_page_was_visited_first", function () {
    $response = $this->get(route('idea.show', $this->idea));

    $this->assertEquals(route('idea.index'), $response['backURL']);
});
