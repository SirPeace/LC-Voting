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


test('filtering_works_when_query_string_in_place', function () {
    $this->get(route('idea.index', ['status' => 'in_progress']))
        ->assertSuccessful()
        ->assertDontSee('<div class="bg-purple text-white text-xxs font-bold uppercase leading-none rounded-full text-center w-28 h-7 py-2 px-4">Considering</div>', false)
        ->assertSee('<div class="bg-yellow text-white text-xxs font-bold uppercase leading-none rounded-full text-center w-28 h-7 py-2 px-4">In Progress</div>', false);
});


test('selected_status_is_highlighted', function () {
    Livewire::withQueryParams(['status' => 'considering'])
        ->test('status-filters')
        ->set('onIndexPage', true)
        ->assertSet('status', 'considering')
        ->assertSeeHTMLInOrder(['border-blue text-gray-900', 'Considering (2)']);
});
