<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Team;
use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        $homeTeam = Team::inRandomOrder()->first() ?? Team::factory()->create();
        $awayTeam = Team::inRandomOrder()->where('id', '!=', $homeTeam->id)->first() ?? Team::factory()->create();
        $week = Week::inRandomOrder()->first() ?? Week::factory()->create();

        return [
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'home_score' => $this->faker->numberBetween(0, 5),
            'away_score' => $this->faker->numberBetween(0, 5),
            'week_id' => $week->id,
            'played' => false, // Default: Not played
        ];
    }
}
