<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Category;
use Database\Seeders\StatusSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();
});


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
    (new CategorySeeder)->run();

    [$categoryOne, $categoryTwo] = Category::whereIn('id', [1, 2])->get();

    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'category_id' => $categoryOne,
    ]);

    Livewire::actingAs($user)
        ->test('edit-idea-modal', ['idea' => $idea])
        ->set('title', 'My Edited Idea')
        ->set('category_id', $categoryTwo->id)
        ->set('description', 'This is my edited idea')
        ->call('updateIdea')
        ->assertEmitted('ideaUpdate');

    $this->assertDatabaseHas('ideas', [
        'title' => 'My Edited Idea',
        'description' => 'This is my edited idea',
        'category_id' => $categoryTwo->id,
    ]);
});


test("authorized_user_can_not_edit_idea_after_one_hour", function () {
    (new CategorySeeder)->run();

    [$categoryOne, $categoryTwo] = Category::whereIn('id', [1, 2])->get();

    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'category_id' => $categoryOne,
        'created_at' => now()->subHour(),
    ]);

    Livewire::actingAs($user)
        ->test('edit-idea-modal', ['idea' => $idea])
        ->set('title', 'My Edited Idea')
        ->set('category_id', $categoryTwo->id)
        ->set('description', 'This is my edited idea')
        ->call('updateIdea')
        ->assertNotEmitted('ideaUpdate');
});
