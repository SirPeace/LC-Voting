<?php

use App\Http\Livewire\CreateComment;
use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


// ! Does not work
// test("idea_comments_livewire_component_renders", function ()
// {
//     $idea = Idea::factory()->create();

//     $commentOne = Comment::factory()->create([
//         'idea_id' => $idea->id,
//         'body' => 'This is my first comment',
//     ]);

//     $response = $this->get(route('idea.show', $idea));

//     $response->assertSeeLivewire('comments');
// });


// ! Does not work
// test("idea_comment_livewire_component_renders", function ()
// {
//     $idea = Idea::factory()->create();

//     $commentOne = Comment::factory()->create([
//         'idea_id' => $idea->id,
//         'body' => 'This is my first comment',
//     ]);

//     $response = $this->get(route('idea.show', $idea));

//     $response->assertSeeLivewire('comment');
// });

test("no_comments_shows_appropriate_message", function ()
{
    $idea = Idea::factory()->create();

    $response = $this->get(route('idea.show', $idea));

    $response->assertSee('No comments yet');
});

test("list_of_comments_shows_on_idea_page", function ()
{
    $idea = Idea::factory()->create();

    $commentOne = Comment::factory()->create([
        'idea_id' => $idea->id,
        'body' => 'This is my first comment',
    ]);

    $commentTwo = Comment::factory()->create([
        'idea_id' => $idea->id,
        'body' => 'This is my second comment',
    ]);

    $response = $this->get(route('idea.show', $idea));

    $response->assertSeeInOrder(['This is my first comment', 'This is my second comment']);
    $response->assertSee('2 comments');
});

test("comments_count_shows_correctly_on_index_page", function()
{
    $idea = Idea::factory()->create();

    $commentOne = Comment::factory()->create([
        'idea_id' => $idea->id,
        'body' => 'This is my first comment',
    ]);

    $commentTwo = Comment::factory()->create([
        'idea_id' => $idea->id,
        'body' => 'This is my second comment',
    ]);

    $response = $this->get(route('idea.index'));

    $response->assertSee('2 comments');
});

test("op_badge_shows_if_author_of_idea_comments_on_idea", function ()
{
    $user = User::factory()->create();

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
    ]);

    $commentOne = Comment::factory()->create([
        'idea_id' => $idea->id,
        'body' => 'This is my first comment',
    ]);

    $commentTwo = Comment::factory()->create([
        'user_id' => $user->id,
        'idea_id' => $idea->id,
        'body' => 'This is my second comment',
    ]);

    $response = $this->get(route('idea.show', $idea));

    $response->assertSee('OP');
});

test("comments_pagination_works", function ()
{
    $idea = Idea::factory()->create();

    $commentOne = Comment::factory()->create([
        'idea_id' => $idea->id
    ]);

    Comment::factory($commentOne->getPerPage())->create([
        'idea_id' => $idea->id,
    ]);

    // dd($commentOne->getPerPage(), Comment::count());

    $response = $this->get(route('idea.show', $idea));

    $response->assertSee($commentOne->body);
    $response->assertDontSee(Comment::find(Comment::count())->body);

    $response = $this->get(route('idea.show', [
        'idea' => $idea,
        'page' => 2,
    ]));

    $response->assertDontSee($commentOne->body);
    $response->assertSee(Comment::find(Comment::count())->body);
});
