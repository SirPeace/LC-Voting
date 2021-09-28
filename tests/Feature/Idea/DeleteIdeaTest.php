<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test("livewire_component_shows_if_user_is_authorized", function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('delete-idea-modal');
});


test("livewire_component_does_not_show_if_user_is_not_authorized", function () {
    $idea = Idea::factory()->create(
        ['user_id' => User::factory()->create()->id]
    );

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('delete-idea-modal');
});


test("authorized_user_can_delete_idea", function () {
    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id
    ]);

    Livewire::actingAs($user)
        ->test('delete-idea-modal', compact('idea'))
        ->call('deleteIdea')
        ->assertRedirect(route('idea.index'));

    $this->assertDatabaseMissing('ideas', ['title' => $idea->title]);
});


test("user_cannot_delete_idea_if_unauthorized", function () {
    $idea = Idea::factory()->create([
        'user_id' => User::factory()->create()->id
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test('delete-idea-modal', compact('idea'))
        ->call('deleteIdea')
        ->assertForbidden();

    $this->assertDatabaseHas('ideas', ['title' => $idea->title]);
});
