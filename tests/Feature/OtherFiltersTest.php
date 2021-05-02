<?php

use App\Models\Idea;
use App\Models\User;
use App\Models\Votable;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $this->userA = User::factory()->create();
    $this->userB = User::factory()->create();

    Idea::factory()->createMany([
        [
            'user_id' => $this->userA->id,
            'category_id' => 1,
            'status_id' => 2, // open
            'title' => 'First idea',
        ],
        [
            'user_id' => $this->userB->id,
            'category_id' => 1,
            'status_id' => 2, // considering
            'title' => 'Second idea',
        ],
        [
            'user_id' => $this->userB->id,
            'category_id' => 2,
            'status_id' => 3,
            'title' => 'Last idea',
        ],
    ]);

    // 2 votes for first, 1 vote for second
    Votable::factory()->createMany([
        [
            'user_id' => $this->userA->id,
            'votable_type' => Idea::class,
            'votable_id' => 1,
        ],
        [
            'user_id' => $this->userB->id,
            'votable_type' => Idea::class,
            'votable_id' => 1,
        ],
        [
            'user_id' => $this->userB->id,
            'votable_type' => Idea::class,
            'votable_id' => 2,
        ],
        [
            'user_id' => $this->userB->id,
            'votable_type' => Idea::class,
            'votable_id' => 3,
        ],
    ]);
});


test('correctly_filters_top_voted_ideas', function () {
    Livewire::test('ideas-index')
        ->set('filter', 'top_voted')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3
                && $ideas->first()->votes_count == 2
                && $ideas->get(1)->votes_count == 1;
        });

    Livewire::withQueryParams(['filter' => 'top_voted'])
        ->test('ideas-index')
        ->assertSet('filter', 'top_voted')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3
                && $ideas->first()->votes_count == 2
                && $ideas->get(1)->votes_count == 1;
        });
});


test('correctly_filters_user_ideas', function () {
    Livewire::actingAs($this->userA)
        ->test('ideas-index')
        ->set('filter', 'user_ideas')
        ->assertViewHas('ideas', fn ($ideas) => $ideas->every(
            fn (Idea $idea) => $idea->user_id == $this->userA->id
        ));

    Livewire::actingAs($this->userB)
        ->withQueryParams(['filter' => 'user_ideas'])
        ->test('ideas-index')
        ->assertSet('filter', 'user_ideas')
        ->assertViewHas('ideas', fn ($ideas) => $ideas->every(
            fn (Idea $idea) => $idea->user_id == $this->userB->id
        ));
});


test('user_ideas_filter_does_not_show_for_guest', function () {
    Livewire::test('ideas-index')
        ->assertDontSeeHtml('<option value="my_ideas">My Ideas</option>');
});


test('user_ideas_filter_works_on_pair_with_status_and_category_filters', function () {
    Livewire::actingAs($this->userB)
        ->test('ideas-index')
        ->set('filter', 'user_ideas')
        ->set('category', 'category_1')
        ->set('status', 'considering')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1
                && $ideas->first()->category_id == 1
                && $ideas->first()->status_id == 2;
        });
});


test('top_voted_filter_works_on_pair_with_status_and_category_filters', function () {
    Livewire::test('ideas-index')
        ->set('filter', 'top_voted')
        ->set('category', 'category_1')
        ->set('status', 'considering')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 2
                && $ideas->first()->votes_count == 2
                && $ideas->every(fn (Idea $idea) => $idea->category_id == 1 && $idea->status_id == 2);
        });
});


test('no_filters_does_not_affect_ideas_list', function () {
    Livewire::test('ideas-index')
        ->set('filter', '')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 3
                && $ideas->first()->title == 'Last idea';
        });
});
