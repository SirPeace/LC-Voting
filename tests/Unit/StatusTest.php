<?php

use App\Models\Idea;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StatusSeeder;

uses(RefreshDatabase::class);
uses(TestCase::class);

beforeEach(function () {
    (new CategorySeeder)->run();
    (new StatusSeeder)->run();

    $this->user = User::factory()->create();
});


it('can_get_right_count_of_ideas_with_each_status', function () {
    Idea::factory()->createMany([
        // 1 * statuses.name = 'open'
        [
            'user_id' => $this->user,
            'status_id' => 1,
        ],
        // 2 * statuses.name = 'considering'
        [
            'user_id' => $this->user,
            'status_id' => 2,
        ],
        [
            'user_id' => $this->user,
            'status_id' => 2,
        ],
        // 3 * statuses.name = 'in_progress'
        [
            'user_id' => $this->user,
            'status_id' => 3,
        ],
        [
            'user_id' => $this->user,
            'status_id' => 3,
        ],
        [
            'user_id' => $this->user,
            'status_id' => 3,
        ],
    ]);

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
