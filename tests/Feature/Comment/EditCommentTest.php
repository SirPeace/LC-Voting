<?php

use App\Models\Idea;
use Livewire\Livewire;
use App\Models\Comment;
use App\Http\Livewire\EditCommentModal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->idea = Idea::factory()->create();
    $this->comment = Comment::factory()->for($this->idea)->create();
});


test('edit-comment-modal livewire component renders on the page', function () {
    $this->get(route('idea.show', ['idea' => $this->idea]))
        ->assertSeeLivewire(EditCommentModal::class);
});


test('only authorized user can edit comment', function () {
    $this->get(route('idea.show', ['idea' => $this->idea]))
        ->assertDontSee('data-test-id="edit-comment-link"', false);

    $this->actingAs($this->comment->user)
        ->get(route('idea.show', ['idea' => $this->idea]))
        ->assertSee('data-test-id="edit-comment-link"', false);
});


test('authorized user can edit comment only in 1 hour', function () {
    $this->comment->update(['created_at' => now()->subHour()]);

    $this->actingAs($this->comment->user)
        ->get(route('idea.show', ['idea' => $this->idea]))
        ->assertDontSee('data-test-id="edit-comment-link"', false);
});


test('the comment is updated correctly', function () {
    Livewire::actingAs($this->comment->user)
        ->test(EditCommentModal::class, ['comment' => $this->comment])
        ->set('body', 'The new comment body')
        ->call('updateComment')
        ->assertEmitted('commentUpdated');

    $this->assertDatabaseHas('comments', [
        'id' => $this->comment->id,
        'body' => 'The new comment body'
    ]);
});


test('component validation works', function () {
    Livewire::actingAs($this->comment->user)
        ->test(EditCommentModal::class, ['comment' => $this->comment])
        ->set('body', 'Few')
        ->call('updateComment')
        ->assertHasErrors(['body'])
        ->assertNotEmitted('commentUpdated');

    $this->assertDatabaseMissing('comments', [
        'id' => $this->comment->id,
        'body' => 'Few'
    ]);
});
