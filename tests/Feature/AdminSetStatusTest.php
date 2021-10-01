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
    $this->admin = User::factory()->admin()->create();
});


test('show_page_contains_set_status_livewire_component_when_user_is_admin', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $this->actingAs($this->admin)
        ->get(route('idea.show', $idea))
        ->assertSeeLivewire('set-status');
});


test('show_page_does_not_contain_set_status_livewire_component_when_user_is_not_admin', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('idea.show', $idea))
        ->assertDontSeeLivewire('set-status');
});


test('initial_status_is_set_correctly', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    Livewire::actingAs($this->admin)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->assertSet('statusID', $idea->status_id);
});


test('can_set_status_correctly_with_no_comment', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $newStatus = Status::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->set('statusID', $newStatus->id)
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'status_id' => $newStatus->id,
    ]);

    $this->assertDatabaseHas('comments', [
        'body' => 'No comment.',
        'status_id' => $newStatus->id,
    ]);
});


test('can_set_status_correctly_with_comment', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $newStatus = Status::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->set('statusID', $newStatus->id)
        ->set('comment', 'Comment body')
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'status_id' => $newStatus->id,
    ]);


    $this->assertDatabaseHas('comments', [
        'body' => 'Comment body',
        'status_id' => $newStatus->id,
    ]);
});


test('can_set_status_correctly_while_notifying_all_voters', function () {
    $idea = Idea::factory()->for($this->admin)->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    Queue::fake();

    Queue::assertNothingPushed();

    Livewire::actingAs($this->admin)
        ->test(SetStatus::class, [
            'idea' => $idea,
        ])
        ->set('statusID', Status::factory()->create()->id)
        ->set('notifyAllVoters', true)
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    Queue::assertPushed(NotifyVoters::class);
});
