<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test("livewire_component_shows_if_user_is_authorized", function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('edit-idea-modal');
});


test("livewire_component_does_not_show_if_user_is_not_authorized", function () {
    $idea = Idea::factory()->create(
        ['user_id' => User::factory()->create()->id]
    );

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('edit-idea-modal');
});


test("livewire_component_form_validation_works", function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test('edit-idea-modal', ['idea' => $idea])
        ->set('title', '')
        ->set('category_id', 0)
        ->set('description', '')
        ->call('updateIdea')
        ->assertHasErrors(['title', 'category_id', 'description'])
        ->assertSee('The title field is required');
});


test("authorized_user_can_edit_idea", function () {
    $user = User::factory()->create();

    $idea = Idea::factory()->for($user)->create();

    $newCategory = Category::factory()->create();

    Livewire::actingAs($user)
        ->test('edit-idea-modal', ['idea' => $idea])
        ->set('title', 'My Edited Idea')
        ->set('category_id', $newCategory->id)
        ->set('description', 'This is my edited idea')
        ->call('updateIdea')
        ->assertEmitted('ideaUpdate');

    $this->assertDatabaseHas('ideas', [
        'title' => 'My Edited Idea',
        'description' => 'This is my edited idea',
        'category_id' => $newCategory->id,
    ]);
});


test("authorized_user_can_not_edit_idea_after_one_hour", function () {
    $user = User::factory()->create();

    $idea = Idea::factory()->for($user)->create([
        'created_at' => now()->subHour(),
    ]);

    $newCategory = Category::factory()->create();

    Livewire::actingAs($user)
        ->test('edit-idea-modal', ['idea' => $idea])
        ->set('title', 'My Edited Idea')
        ->set('category_id', $newCategory->id)
        ->set('description', 'This is my edited idea')
        ->call('updateIdea')
        ->assertNotEmitted('ideaUpdate');
});
