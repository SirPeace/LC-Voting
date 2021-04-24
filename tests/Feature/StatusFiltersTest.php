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

    Idea::factory()->createMany([
        // 2 * statuses.name = 'considering'
        [
            'user_id' => User::factory()->create(),
            'status_id' => 2,
        ],
        [
            'user_id' => User::factory()->create(),
            'status_id' => 2,
        ],
        // 3 * statuses.name = 'in_progress'
        [
            'user_id' => User::factory()->create(),
            'status_id' => 3,
        ],
        [
            'user_id' => User::factory()->create(),
            'status_id' => 3,
        ],
        [
            'user_id' => User::factory()->create(),
            'status_id' => 3,
        ],
    ]);
});


test('filtering_works_when_query_string_parameter_is_set', function () {
    Livewire::withQueryParams(['status' => 'in_progress'])
        ->test('ideas-index')
        ->assertSet('status', 'in_progress')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3
                && $ideas->every(fn ($idea) => $idea->status_id == 3);
        })
        ->assertSeeHtmlInOrder(['bg-yellow', 'In Progress']);
});


test('selected_status_is_highlighted', function () {
    Livewire::withQueryParams(['status' => 'considering'])
        ->test('status-filters')
        ->set('onIndexPage', true)
        ->assertSet('status', 'considering')
        ->assertSeeHTMLInOrder(['border-blue text-gray-900', 'Considering (2)']);
});
