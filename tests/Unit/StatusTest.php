<?php

use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UserSeeder;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    (new StatusSeeder)->run();
    (new CategorySeeder)->run();
    (new UserSeeder)->run();
});


it('can_get_right_count_of_ideas_with_each_status', function () {
    // 1 open
    Idea::factory()->existing()->create(['status_id' => 1]);
    // 2 considering
    Idea::factory(2)->existing()->create(['status_id' => 2]);
    // 3 in_progress
    Idea::factory(3)->existing()->create(['status_id' => 3]);

    $statusesCount = Status::getStatusesCount();

    $this->assertArrayHasKey('open', $statusesCount);
    $this->assertArrayHasKey('considering', $statusesCount);
    $this->assertArrayHasKey('in_progress', $statusesCount);
    $this->assertArrayHasKey('implemented', $statusesCount);
    $this->assertArrayHasKey('closed', $statusesCount);

    $this->assertEquals(1, $statusesCount['open']);
    $this->assertEquals(2, $statusesCount['considering']);
    $this->assertEquals(3, $statusesCount['in_progress']);
});
