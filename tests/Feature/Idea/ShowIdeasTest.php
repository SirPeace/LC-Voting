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
    Idea::factory(Idea::PAGINATION_COUNT - 2)->create([
        'user_id' => $this->user->id,
    ]);

    $ideaLast = Idea::factory()->create([
        'title' => 'My Last Idea',
        'description' => 'Description of my last idea',
        'user_id' => $this->user->id,
    ]);

    $this->get(route('idea.index'))
        ->assertSee($ideaLast->title)
        ->assertDontSee($this->ideaOne->title);

    $this->get(route('idea.index', ['page' => 2]))
        ->assertSee($this->ideaOne->title)
        ->assertDontSee($ideaLast->title);
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


test('in_app_back_button_works_when_index_page_visited_first', function () {
    $user = User::factory()->create();

    $ideaOne = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Idea',
        'description' => 'Description of my first idea',
    ]);


    $response = $this->get('/?category=Category%202&status=Considering');
    $response = $this->get(route('idea.show', $ideaOne));

    $this->assertStringContainsString(
        '/?category=Category%202&status=Considering',
        $response['backURL']
    );
});


test('in_app_back_button_works_when_show_page_only_page_visited', function ()
{
    $user = User::factory()->create();

    $ideaOne = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Idea',
        'description' => 'Description of my first idea',
    ]);

    $response = $this->get(route('idea.show', $ideaOne));

    $this->assertEquals(route('idea.index'), $response['backURL']);
});
