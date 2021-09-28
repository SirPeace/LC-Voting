<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Status;
use Livewire\Livewire;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new StatusSeeder)->run();

    $this->admin = User::factory()->admin()->create();
    $this->idea = Idea::factory()->for(Status::first())->create();
});


test('status_component_is_only_visible_to_admin', function () {
    $this->get(route('idea.show', ['idea' => $this->idea]))
        ->assertDontSeeLivewire('set-status');

    $this->actingAs($this->admin)
        ->get(route('idea.show', ['idea' => $this->idea]))
        ->assertSeeLivewire('set-status');
});


test('only_admin_can_set_idea_status', function () {
    Livewire::test('set-status', ['idea' => $this->idea])
        ->assertSet('statusID', 1)
        ->set('statusID', 2)
        ->call('setStatusID')
        ->assertNotEmitted('statusUpdate');

    Livewire::actingAs(User::factory()->create())
        ->test('set-status', ['idea' => $this->idea])
        ->assertSet('statusID', 1)
        ->set('statusID', 3)
        ->call('setStatusID')
        ->assertNotEmitted('statusUpdate');

    $this->idea->refresh();

    Livewire::test('idea-show', ['idea' => $this->idea])
        ->assertSet('idea', fn (Idea $idea) => $idea->status_id == 1);

    Livewire::actingAs($this->admin)
        ->test('set-status', ['idea' => $this->idea])
        ->assertSet('statusID', 1)
        ->set('statusID', 2)
        ->call('setStatusID')
        ->assertEmitted('statusUpdate');

    $this->idea->refresh();

    Livewire::test('idea-show', ['idea' => $this->idea])
        ->assertSet('idea', fn (Idea $idea) => $idea->status_id == 2);
});
