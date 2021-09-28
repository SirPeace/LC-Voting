<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Http\Response;
use App\Http\Livewire\IdeaShow;
use App\Http\Livewire\IdeaIndex;
use App\Http\Livewire\MarkIdeaAsSpamModal;
use App\Http\Livewire\MarkIdeaAsNotSpamModal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});


test('shows_mark_idea_as_spam_livewire_component_when_user_has_authorization', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create();

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('mark-idea-as-spam-modal');
});

test('does_not_show_mark_idea_as_spam_livewire_component_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    $this->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('mark-idea-as-spam-modal');
});

test('marking_an_idea_as_spam_works_when_user_has_authorization', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create();

    Livewire::actingAs($user)
        ->test(MarkIdeaAsSpamModal::class, [
            'idea' => $idea,
        ])
        ->call('markAsSpam')
        ->assertEmitted('ideaWasMarkedAsSpam');

    $this->assertCount(1, Idea::first()->spamMarks);
});

test('marking_an_idea_as_spam_does_not_work_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    Livewire::test(MarkIdeaAsSpamModal::class, [
        'idea' => $idea,
    ])
        ->call('markAsSpam')
        ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('marking_an_idea_as_spam_shows_on_menu_when_user_has_authorization', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create();

    Livewire::actingAs($user)
        ->test(IdeaShow::class, [
            'idea' => $idea,
            'votesCount' => 4,
        ])
        ->assertSee('Mark as Spam');
});

test('marking_an_idea_as_spam_does_not_show_on_menu_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    Livewire::test(IdeaShow::class, [
        'idea' => $idea,
        'votesCount' => 4,
    ])
        ->assertDontSee('Mark as Spam');
});

test('shows_mark_idea_as_not_spam_livewire_component_when_user_has_authorization', function () {
    $idea = Idea::factory()->create();

    $this->actingAs($this->admin)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('mark-idea-as-not-spam-modal');
});

test('does_not_show_mark_idea_as_not_spam_livewire_component_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    $this->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('mark-idea-as-not-spam-modal');
});

test('marking_an_idea_as_not_spam_works_when_user_has_authorization', function () {
    $idea = Idea::factory()->create();
    foreach (range(1, 4) as $i) {
        $idea->spamMarks()->save(User::factory()->create());
    }

    Livewire::actingAs($this->admin)
        ->test(MarkIdeaAsNotSpamModal::class, [
            'idea' => $idea,
        ])
        ->call('markAsNotSpam')
        ->assertEmitted('ideaWasMarkedAsNotSpam');

    $this->assertCount(0, Idea::first()->spamMarks);
});

test('marking_an_idea_as_not_spam_does_not_work_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    Livewire::test(MarkIdeaAsNotSpamModal::class, [
        'idea' => $idea,
    ])
        ->call('markAsNotSpam')
        ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('marking_an_idea_as_not_spam_shows_on_menu_when_user_has_authorization', function () {
    $idea = Idea::factory()->create();
    $idea->spamMarks()->save(User::factory()->create());

    Livewire::actingAs($this->admin)
        ->test(IdeaShow::class, [
            'idea' => $idea,
            'votesCount' => 4,
        ])
        ->assertSee('Not Spam');
});

test('marking_an_idea_as_not_spam_does_not_show_on_menu_when_user_does_not_have_authorization', function () {
    $idea = Idea::factory()->create();

    Livewire::test(IdeaShow::class, [
        'idea' => $idea,
        'votesCount' => 4,
    ])
        ->assertDontSee('Not Spam');
});

test('spam_reports_count_shows_on_idea_index_page_if_logged_in_as_admin', function () {
    $idea = Idea::factory()->create();
    foreach (range(1, 3) as $i) {
        $idea->spamMarks()->save(User::factory()->create());
    }

    Livewire::actingAs($this->admin)
        ->test(IdeaIndex::class, [
            'idea' => $idea,
            'votesCount' => 4,
        ])
        ->assertSee('Spam Reports: 3');
});

test('spam_reports_count_shows_on_idea_show_page_if_logged_in_as_admin', function () {
    $idea = Idea::factory()->create();
    foreach (range(1, 3) as $i) {
        $idea->spamMarks()->save(User::factory()->create());
    }

    Livewire::actingAs($this->admin)
        ->test(IdeaShow::class, [
            'idea' => $idea,
            'votesCount' => 4,
        ])
        ->assertSee('Spam Reports: 3');
});
