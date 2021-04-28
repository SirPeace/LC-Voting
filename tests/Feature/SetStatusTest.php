<?php

use App\Models\Idea;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $this->admin = User::factory()->create([
        'name' => 'Roman Khabibulin',
        'email' => 'roman.khabibulin12@gmail.com',
    ]);

    $this->idea = Idea::factory()->create([
        'title' => 'Quick brown fox',
        'status_id' => 1,
        'user_id' => $this->admin->id,
    ]);
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
