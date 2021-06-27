<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $this->user = User::factory()->create();

    $this->categoryOne = Category::find(1);
    $this->categoryTwo = Category::find(2);

    $this->statusOpen = Status::where('name', 'open')->first();
    $this->statusConsidering = Status::where('name', 'considering')->first();

    [$this->ideaOne, $this->ideaTwo] = Idea::factory()->createMany([
        [
            'title' => 'My First Idea',
            'description' => 'Description of my first idea',
            'category_id' => $this->categoryOne,
            'status_id' => $this->statusOpen,
            'user_id' => $this->user->id,
        ],
        [
            'title' => 'My Second Idea',
            'description' => 'Description of my second idea',
            'category_id' => $this->categoryTwo,
            'status_id' => $this->statusConsidering,
            'user_id' => $this->user->id,
        ]
    ]);
});


test("list_of_ideas_shows_on_main_page", function () {
    $this->get(route('idea.index'))
        ->assertSuccessful()

        ->assertSee($this->ideaOne->title)
        ->assertSee($this->ideaOne->description)
        ->assertSee($this->categoryOne->alias)

        ->assertSee($this->ideaTwo->title)
        ->assertSee($this->ideaTwo->description)
        ->assertSee($this->categoryTwo->alias);
});


test("single_idea_shows_correctly_on_the_show_page", function () {
    $this->get(route('idea.show', $this->ideaOne))
        ->assertSuccessful()

        ->assertSee($this->ideaOne->title)
        ->assertSee($this->ideaOne->description)
        ->assertSee($this->categoryOne->alias);
});

test("ideas_pagination_works", function () {
    $ideaOne = Idea::factory()->create();

    Idea::factory($ideaOne->getPerPage())->create();

    $response = $this->get('/');

    $response->assertSee(Idea::find(Idea::count())->title);
    $response->assertDontSee($ideaOne->title);

    $response = $this->get('/?page=2');

    $response->assertDontSee(Idea::find(Idea::count())->title);
    $response->assertSee($ideaOne->title);
});

test("same_idea_title_different_slugs", function () {
    $newIdea = Idea::factory()->create([
        'title' => 'My First Idea',
        'description' => 'Description of my new idea',
        'user_id' => $this->user->id,
    ]);

    $this->get(route('idea.show', $this->ideaOne))->assertSuccessful();
    $this->assertTrue(request()->path() === 'ideas/my-first-idea');

    $this->get(route('idea.show', $newIdea))->assertSuccessful();
    $this->assertTrue(request()->path() === 'ideas/my-first-idea-1');
});
