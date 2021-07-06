<?php

namespace Tests\Feature;

use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use Livewire\Livewire;
use App\Models\Category;
use App\Jobs\NotifyVoters;
use App\Http\Livewire\SetStatus;
use Database\Seeders\CategorySeeder;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
});


test('show_page_contains_set_status_livewire_component_when_user_is_admin', function ()
{
    $user = User::factory()->create([
        'email' => 'admin@mail.com',
    ]);

    $statusOpen = Status::factory()->create(['alias' => 'Open']);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'status_id' => $statusOpen->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('set-status');
});


test('show_page_does_notcontain_set_status_livewire_component_when_user_is_not_admin', function ()
{
    $user = User::factory()->create();

    $statusOpen = Status::factory()->create(['alias' => 'Open']);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'status_id' => $statusOpen->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $this->actingAs($user)
        ->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('set-status');
});


test('initial_status_is_set_correctly', function ()
{
    $user = User::factory()->create([
        'email' => 'admin@mail.com',
    ]);

    $statusConsidering = Status::factory()->create(['id' => 2, 'alias' => 'Considering']);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'status_id' => $statusConsidering->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    Livewire::actingAs($user)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->assertSet('statusID', $statusConsidering->id);
});


test('can_set_status_correctly', function ()
{
    $user = User::factory()->create([
        'email' => 'admin@mail.com',
    ]);
    $categoryTwo = Category::factory()->create(['alias' => 'Category 2']);

    $statusConsidering = Status::factory()->create(['id' => 2, 'alias' => 'Considering']);
    $statusInProgress = Status::factory()->create(['id' => 3, 'alias' => 'In Progress']);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'status_id' => $statusConsidering->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    Livewire::actingAs($user)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->set('statusID', $statusInProgress->id)
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'status_id' => $statusInProgress->id,
    ]);
});


test('can_set_status_correctly_while_notifying_all_voters', function ()
{
    $user = User::factory()->create([
        'email' => 'admin@mail.com',
    ]);

    $statusConsidering = Status::factory()->create(['id' => 2, 'alias' => 'Considering']);
    $statusInProgress = Status::factory()->create(['id' => 3, 'alias' => 'In Progress']);

    $idea = Idea::factory()->create([
        'user_id' => $user->id,
        'status_id' => $statusConsidering->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    Queue::fake();

    Queue::assertNothingPushed();

    Livewire::actingAs($user)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->set('statusID', $statusInProgress->id)
        ->set('notifyAllVoters', true)
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    Queue::assertPushed(NotifyVoters::class);
});
