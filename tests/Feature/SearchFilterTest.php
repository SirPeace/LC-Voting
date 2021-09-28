<?php

namespace Tests\Feature;

use App\Http\Livewire\IdeasIndex;
use App\Models\Category;
use App\Models\Idea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);


test('searching_works_when_more_than_3_characters', function () {
    $ideaOne = Idea::factory()->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $ideaTwo = Idea::factory()->create([
        'title' => 'My Second Idea',
        'description' => 'Description for my second idea',
    ]);

    Livewire::test(IdeasIndex::class)
        ->set('search', 'Second')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 1
                && $ideas->first()->title === 'My Second Idea';
        });
});


test('does_not_perform_search_if_less_than_3_characters', function () {
    $ideaOne = Idea::factory()->create([
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $ideaTwo = Idea::factory()->create([
        'title' => 'My Second Idea',
        'description' => 'Description for my second idea',
    ]);

    Livewire::test(IdeasIndex::class)
        ->set('search', 'Se')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 2;
        });
});


test('search_works_correctly_with_category_filters', function () {
    $category = Category::factory()->create(['name' => 'category_1']);

    $ideaOne = Idea::factory()->create([
        'category_id' => $category->id,
        'title' => 'My First Idea',
        'description' => 'Description for my first idea',
    ]);

    $ideaTwo = Idea::factory()->create([
        'category_id' => $category->id,
        'title' => 'My Second Idea',
        'description' => 'Description for my first idea',
    ]);

    $ideaThree = Idea::factory()->create([
        'title' => 'My Third Idea',
        'description' => 'Description for my first idea',
    ]);

    Livewire::test(IdeasIndex::class)
        ->set('category', 'category_1')
        ->set('search', 'Idea')
        ->assertViewHas('ideas', function ($ideas) {
            return $ideas->count() === 2;
        });
});
