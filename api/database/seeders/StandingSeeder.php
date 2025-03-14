<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Standing;

class StandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        foreach ($teams as $team) {
            Standing::firstOrCreate([
                'team_id' => $team->id
            ], [
                'won' => 0,
                'lose' => 0,
                'draw' => 0,
                'points' => 0,
                'goal_difference'=>0,
                'goals_conceded'=>0,
                'goals_scored'=>0
            ]);
        }
    }
}
