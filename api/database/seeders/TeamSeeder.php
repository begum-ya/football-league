<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create(['name' => 'Liverpool', 'power' => 90]);
        Team::create(['name' => 'Manchester City', 'power' => 95]);
        Team::create(['name' => 'Chelsea', 'power' => 85]);
        Team::create(['name' => 'Arsenal', 'power' => 80]);

    }
}
