<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskList;

class DefaultTaskListSeeder extends Seeder
{
    public function run(): void
    {
        TaskList::firstOrCreate(
            ['name' => 'Nhiệm vụ của tôi']
        );
    }
}
