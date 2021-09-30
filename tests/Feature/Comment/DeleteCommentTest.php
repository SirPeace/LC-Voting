<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Comment;
use App\Http\Livewire\DeleteCommentModal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->idea = Idea::factory()->create();
    $this->comment = Comment::factory()->for($this->idea)->create();
});


test('delete-comment-modal livewire component renders on the page if user is authenticated', function () {
    $this->get(route('idea.show', ['idea' => $this->idea]))
        ->assertDontSeeLivewire(DeleteCommentModal::class);

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', ['idea' => $this->idea]))
        ->assertSeeLivewire(DeleteCommentModal::class);
});


test('only authorized user can delete comment', function () {
    $this->get(route('idea.show', ['idea' => $this->idea]))
        ->assertDontSee('data-test-id="delete-comment-link"', false);

    Livewire::test(DeleteCommentModal::class, ['comment' => $this->comment])
        ->call('deleteComment')
        ->assertNotEmitted('commentDeleted');

    $this->actingAs($this->comment->user)
        ->get(route('idea.show', ['idea' => $this->idea]))
        ->assertSee('data-test-id="delete-comment-link"', false);
});


test('the comment is updated correctly', function () {
    Livewire::actingAs($this->comment->user)
        ->test(DeleteCommentModal::class, ['comment' => $this->comment])
        ->call('deleteComment')
        ->assertEmitted('commentDeleted');

    $this->assertDatabaseMissing('comments', [
        'id' => $this->comment->id,
    ]);
});
