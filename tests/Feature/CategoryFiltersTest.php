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

    $user = User::factory()->create();

    $openStatusID = 1;
    $consideringStatusID = 2;
    $inProgressStatusID = 3;

    Idea::factory()->createMany([
        [
            'user_id' => $user,
            'category_id' => 1,
            'status_id' => $openStatusID,
            'title' => 'First idea',
        ],
        [
            'user_id' => $user,
            'category_id' => 2,
            'status_id' => $consideringStatusID,
            'title' => 'Second idea',
        ],
        [
            'user_id' => $user,
            'category_id' => 2,
            'status_id' => $openStatusID,
            'title' => 'Third idea',
        ],
        [
            'user_id' => $user,
            'category_id' => 3,
            'status_id' => $openStatusID,
            'title' => 'Fourth idea',
        ],
        [
            'user_id' => $user,
            'category_id' => 3,
            'status_id' => $consideringStatusID,
            'title' => 'Fifth idea',
        ],
        [
            'user_id' => $user,
            'category_id' => 3,
            'status_id' => $inProgressStatusID,
            'title' => 'Sixth idea',
        ],
    ]);
});


test('filtering_ideas_if_category_component_parameter_is_set', function () {
    Livewire::test('ideas-index')
        ->set('category', 'category_1')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::test('ideas-index')
        ->set('category', 'category_2')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 2;
        });

    Livewire::test('ideas-index')
        ->set('category', 'category_3')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3;
        });
});


test('filtering_ideas_if_category_query_string_parameter_is_set', function () {
    Livewire::withQueryParams(['category' => 'category_1'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::withQueryParams(['category' => 'category_2'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 2
                && $ideas->every(fn ($idea) => $idea->category_id == 2);
        });

    Livewire::withQueryParams(['category' => 'category_3'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3
                && $ideas->every(fn ($idea) => $idea->category_id == 3);
        });
});


test('displaying_all_ideas_if_category_component_parameter_is_set_to_empty_string', function () {
    Livewire::test('ideas-index')
        ->set('category', '')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 6;
        });
});


test('displaying_all_ideas_if_category_query_string_parameter_is_set_to_empty_string', function () {
    Livewire::withQueryParams(['category' => ''])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 6;
        });
});


test('filtering_ideas_correctly_if_both_category_and_status_component_parameters_are_set', function () {
    Livewire::test('ideas-index')
        ->set('category', 'category_1')
        ->set('status', 'open')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::test('ideas-index')
        ->set('category', 'category_2')
        ->set('status', 'considering')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::test('ideas-index')
        ->set('category', 'category_3')
        ->set('status', 'in_progress')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });
});


test('filtering_ideas_correctly_if_both_category_and_status_query_string_parameters_are_set', function () {
    Livewire::withQueryParams(['category' => 'category_1', 'status' => 'open'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::withQueryParams(['category' => 'category_2', 'status' => 'considering'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });

    Livewire::withQueryParams(['category' => 'category_3', 'status' => 'in_progress'])
        ->test('ideas-index')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1;
        });
});
