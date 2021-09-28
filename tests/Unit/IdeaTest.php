<?php

use Tests\TestCase;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    [$this->userOne, $this->userTwo] = User::factory(2)->create();

    $this->idea = Idea::factory()->create([
        "user_id" => User::factory()->create()->id,
    ]);
});


it("can_check_if_specified_user_voted", function () {
    DB::table('votes')->insert([
        "user_id" => $this->userOne->id,
        "idea_id" => $this->idea->id,
    ]);
    DB::table('votes')->insert([
        "user_id" => $this->userOne->id,
        "idea_id" => Idea::factory()->create()->id,
    ]);

    DB::table('votes')->insert([
        "user_id" => $this->userTwo->id,
        "idea_id" => Idea::factory()->create()->id,
    ]);

    $this->assertTrue($this->idea->voters->contains($this->userOne));
    $this->assertFalse($this->idea->voters->contains($this->userTwo));
});


it("can_be_voted_by_user", function () {
    $this->actingAs($this->userOne);

    $this->idea->voters()->attach($this->userOne);

    $this->assertTrue($this->idea->voters->contains($this->userOne));
    $this->assertFalse($this->idea->voters->contains($this->userTwo));
});


it("can_be_voted_only_once_by_same_user", function () {
    $this->actingAs($this->userOne);

    $this->idea->voters()->attach($this->userOne);

    $this->expectException(QueryException::class);
    $this->idea->voters()->attach($this->userOne);

    $this->assertTrue(
        DB::table('votes')
            ->where([
                'user_id' => $this->userOne->id,
                'idea_id' => $this->idea->id
            ])
            ->count() === 1
    );
});
