<?php

use App\Models\Idea;
use App\Models\User;
use Livewire\Livewire;
use App\Http\Livewire\CommentNotifications;
use App\Http\Livewire\CreateComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;

uses(RefreshDatabase::class);


test('comment notifications livewire component renders when user logged in', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('idea.index'))
        ->assertSeeLivewire('comment-notifications');
});


test('comment notifications livewire component does not render when user not logged in', function () {
    $this->get(route('idea.index'))
        ->assertDontSeeLivewire('comment-notifications');
});


test('notifications show for logged in user', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the first comment')
        ->call('addComment');

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the second comment')
        ->call('addComment');

    DatabaseNotification::first()->update(['created_at' => now()->subMinute()]);

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->assertSeeInOrder([
            'This is the second comment',
            'This is the first comment'
        ])
        ->assertSet('notificationsCount', 2);
});


test('notification count greater than threshold shows for logged in user', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();
    $threshold = CommentNotifications::NOTIFICATIONS_THRESHOLD;

    foreach (range(1, $threshold + 1) as $_) {
        Livewire::actingAs(User::factory()->create())
            ->test(CreateComment::class, compact('idea'))
            ->set('comment', 'This is the first comment')
            ->call('addComment');
    }

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->assertSet('notificationsCount', "$threshold+")
        ->assertSee("$threshold+");
});


test('can mark all notifications as read', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the first comment')
        ->call('addComment');

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the second comment')
        ->call('addComment');

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->call('markAllAsRead');

    $this->assertEquals(0, $user->fresh()->unreadNotifications->count());
});


test('can mark individual notification as read', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the first comment')
        ->call('addComment');

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the second comment')
        ->call('addComment');

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->call('navigateAndMarkAsRead', DatabaseNotification::first()->id)
        ->assertRedirect(route('idea.show', ['idea' => $idea, 'page' => 1]));

    $this->assertEquals(1, $user->fresh()->unreadNotifications->count());
});


test('notification idea deleted redirects to index page', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the first comment')
        ->call('addComment');

    $idea->delete();

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->call('navigateAndMarkAsRead', DatabaseNotification::first()->id)
        ->assertRedirect(route('idea.index'));

    $this->assertNotNull(session('error_message'));
});


test('notification comment deleted redirects to index page', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CreateComment::class, compact('idea'))
        ->set('comment', 'This is the first comment')
        ->call('addComment');

    $idea->comments()->delete();

    Livewire::actingAs($user)
        ->test(CommentNotifications::class)
        ->call('getNotifications')
        ->call('navigateAndMarkAsRead', DatabaseNotification::first()->id)
        ->assertRedirect(route('idea.index'));

    $this->assertNotNull(session('error_message'));
});
