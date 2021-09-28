<?php

namespace Tests\Unit\Jobs;

use App\Jobs\NotifyVoters;
use App\Mail\IdeaStatusChange;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(RefreshDatabase::class);
uses(TestCase::class);

test('it_sends_an_email_to_all_voters', function () {
    $user = User::factory()->create([
        'email' => 'andre_madarang@hotmail.com',
    ]);

    $userB = User::factory()->create([
        'email' => 'user@user.com',
    ]);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $idea->votes()->save($user);
    $idea->votes()->save($userB);

    Mail::fake();

    NotifyVoters::dispatch($idea);

    Mail::assertQueued(IdeaStatusChange::class, function ($mail) {
        return $mail->hasTo('andre_madarang@hotmail.com')
            && $mail->build()->subject === 'An idea you voted for changed the status';
    });

    Mail::assertQueued(IdeaStatusChange::class, function ($mail) {
        return $mail->hasTo('user@user.com')
            && $mail->build()->subject === 'An idea you voted for changed the status';
    });
});
