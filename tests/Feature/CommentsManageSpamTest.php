<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Comment;
use Illuminate\Http\Response;
use App\Http\Livewire\IdeaShow;
use App\Http\Livewire\IdeaIndex;
use App\Http\Livewire\MarkCommentAsSpamModal;
use App\Http\Livewire\MarkCommentAsNotSpamModal;
use App\Http\Livewire\Comment as CommentComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});


test('shows mark-comment-as-spam component when user is authenticated', function () {
    $idea = Idea::factory()->create();

    $this->get(route('idea.show', $idea))
        ->assertDontSeeLivewire(MarkCommentAsSpamModal::class);

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire(MarkCommentAsSpamModal::class);
});


test('can mark comment as spam when authenticated', function () {
    $comment = Comment::factory()->create();

    Livewire::test(MarkCommentAsSpamModal::class, compact('comment'))
        ->call('markAsSpam')
        ->assertNotEmitted('commentWasMarkedAsSpam')
        ->assertStatus(Response::HTTP_FORBIDDEN);

    $this->assertCount(0, $comment->refresh()->spamMarks);

    Livewire::actingAs(User::factory()->create())
        ->test(MarkCommentAsSpamModal::class, compact('comment'))
        ->call('markAsSpam')
        ->assertEmitted('commentWasMarkedAsSpam');

    $this->assertCount(1, $comment->refresh()->spamMarks);
});


test('marking comment as spam shows on menu when user is authenticated', function () {
    $comment = Comment::factory()->create();

    Livewire::test(CommentComponent::class, compact('comment'))
        ->assertDontSee('data-test-id="mark-comment-as-spam-link"', false);

    Livewire::actingAs(User::factory()->create())
        ->test(CommentComponent::class, compact('comment'))
        ->assertSee('data-test-id="mark-comment-as-spam-link"', false);
});


test('shows mark-comment-as-not-spam component if user is admin', function () {
    $idea = Idea::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', $idea))
        ->assertDontSeeLivewire(MarkCommentAsNotSpamModal::class);

    $this->actingAs($this->admin)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire(MarkCommentAsNotSpamModal::class);
});


test('marking comment as not spam works when user is admin', function () {
    $comment = Comment::factory()
        ->has(User::factory()->count(4), 'spamMarks')
        ->create();

    Livewire::test(MarkCommentAsNotSpamModal::class, compact('comment'))
        ->call('markAsNotSpam')
        ->assertNotEmitted('commentWasMarkedAsNotSpam')
        ->assertStatus(Response::HTTP_FORBIDDEN);

    $this->assertCount(4, $comment->refresh()->spamMarks);

    Livewire::actingAs($this->admin)
        ->test(MarkCommentAsNotSpamModal::class, compact('comment'))
        ->call('markAsNotSpam')
        ->assertEmitted('commentWasMarkedAsNotSpam');

    $this->assertCount(0, $comment->refresh()->spamMarks);
});


test('marking comment as not spam shows on menu if user is admin', function () {
    $comment = Comment::factory()
        ->has(User::factory(), 'spamMarks')
        ->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CommentComponent::class, compact('comment'))
        ->assertDontSee('Not Spam');

    Livewire::actingAs($this->admin)
        ->test(CommentComponent::class, compact('comment'))
        ->assertSee('Not Spam');
});


test('spam reports count shows in comment component if logged in as admin', function () {
    $comment = Comment::factory()
        ->has(User::factory()->count(3), 'spamMarks')
        ->create();

    Livewire::actingAs($this->admin)
        ->test(CommentComponent::class, compact('comment'))
        ->assertSee('Spam Reports: 3');
});
