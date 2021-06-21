<?php

use App\Models\Idea;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);


test("create_idea_form_does_not_show_when_logged_out", function () {
    $this->get(route("idea.index"))
        ->assertSee("Please login to create an idea")
        ->assertDontSeeLivewire("create-idea");
});


test("create_idea_form_shows_when_logged_in", function () {
    $this->actingAs(User::factory()->create());

    $this->get(route("idea.index"))
        ->assertDontSee("Please login to create an idea")
        ->assertSee("Let us know what you would like and we'll take a look over!", false)
        ->assertSeeLivewire("create-idea");
});


test("form_shows_error_messages_if_validation_fails", function () {
    (new StatusSeeder)->run();
    (new CategorySeeder)->run();

    Livewire::actingAs(User::factory()->create())
        ->test("create-idea")
        ->set("title", "")
        ->set("description", "123")
        ->call("createIdea")
        ->assertSee("The title field is required")
        ->assertSee("The description must be at least 10 characters");
});


test("form_does_not_create_new_records_if_validation_fails", function () {
    (new StatusSeeder)->run();
    (new CategorySeeder)->run();

    $ideasCountBefore = Idea::count();

    Livewire::actingAs(User::factory()->create())
        ->test("create-idea")
        ->set("title", "")
        ->set("description", "")
        ->set("category_id", "")
        ->call("createIdea");

    $ideasCountAfter = Idea::count();

    $this->assertEquals($ideasCountBefore, $ideasCountAfter);
});


test("success_message_is_shown_and_database_record_is_persisted_if_validation_passes", function () {
    (new StatusSeeder)->run();
    (new CategorySeeder)->run();

    $ideaTitle = "The new idea title";
    $ideaDescription = "The new idea description";
    $ideaCategory = 3;

    $ideasCountBefore = Idea::count();

    Livewire::actingAs(User::factory()->create())
        ->test("create-idea")
        ->set("title", $ideaTitle)
        ->set("description", $ideaDescription)
        ->set("category_id", $ideaCategory)
        ->call("createIdea")
        ->assertRedirect(route("idea.index"));

    $ideasCountAfter = Idea::count();

    $this->assertEquals($ideasCountBefore + 1, $ideasCountAfter);

    $this->assertDatabaseHas("ideas", [
        "title" => $ideaTitle,
        "category_id" => $ideaCategory,
        "description" => $ideaDescription,
    ]);

    $this->get(route("idea.index"))
        ->assertSee("Idea was successfully created")
        ->assertSee($ideaTitle)
        ->assertSee($ideaDescription);
});
