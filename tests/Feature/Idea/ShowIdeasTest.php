<?php

namespace Tests\Feature\Idea;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test("list_of_ideas_shows_on_main_page", function () {
    [$ideaOne, $ideaTwo] = Idea::factory(2)->create();

    $this->get(route('idea.index'))
        ->assertSuccessful()
        ->assertSee($ideaOne->title)
        ->assertSee($ideaOne->description)
        ->assertSee($ideaTwo->title)
        ->assertSee($ideaTwo->description);
});


test("single_idea_shows_correctly_on_the_show_page", function () {
    $idea = Idea::factory()->create();

    $this->get(route('idea.show', $idea))
        ->assertSuccessful()
        ->assertSee($idea->title)
        ->assertSee($idea->description);
});

test("ideas_pagination_works", function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $firstIdea = Idea::factory()->existing()->create([
        'user_id' => User::factory()->create(),
    ]);

    Idea::factory()->existing()->count($firstIdea->getPerPage() - 1)->create([
        'user_id' => User::factory()->create(),
    ]);

    $lastIdea = Idea::factory()->existing()->create([
        'user_id' => User::factory()->create(),
    ]);

    $this->get(route('idea.index'))
        ->assertSee($lastIdea->title)
        ->assertDontSee($firstIdea->title);

    $this->get(route('idea.index', ['page' => 2]))
        ->assertSee($firstIdea->title)
        ->assertDontSee($lastIdea->title);
});

test("ideas_with_same_title_have_different_slugs", function () {
    $idea = Idea::factory()->create([
        'title' => 'My First Idea',
        'user_id' => User::factory()->create(),
    ]);

    $newIdea = Idea::factory()->create([
        'title' => 'My First Idea',
        'user_id' => User::factory()->create(),
    ]);

    $this->get(route('idea.show', $idea))->assertSuccessful();
    $this->assertTrue(request()->path() === 'ideas/my-first-idea');

    $this->get(route('idea.show', $newIdea))->assertSuccessful();
    $this->assertTrue(request()->path() === 'ideas/my-first-idea-2');
});


test('in_app_back_button_works_when_index_page_visited_first', function () {
    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Idea',
        'description' => 'Description of my first idea',
    ]);

    $response = $this->get('/?category=Category%202&status=Considering');
    $response = $this->get(route('idea.show', $idea));

    $this->assertStringContainsString(
        '/?category=Category%202&status=Considering',
        $response['backURL']
    );
});


test('in_app_back_button_works_when_show_page_only_page_visited', function () {
    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Idea',
        'description' => 'Description of my first idea',
    ]);

    $response = $this->get(route('idea.show', $idea));

    $this->assertEquals(route('idea.index'), $response['backURL']);
});
