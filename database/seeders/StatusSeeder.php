<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::factory()->createMany([
            [
                'name' => 'open',
                'alias' => 'Open',
            ],
            [
                'name' => 'considering',
                'alias' => 'Considering',
            ],
            [
                'name' => 'in_progress',
                'alias' => 'In Progress',
            ],
            [
                'name' => 'implemented',
                'alias' => 'Implemented',
            ],
            [
                'name' => 'closed',
                'alias' => 'Closed',
            ],
        ]);
    }
}
