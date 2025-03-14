<?php

namespace Database\Factories;

use App\Models\Standing;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandingFactory extends Factory
{
    protected $model = Standing::class;

    public function definition()
    {
        return [
           'team_id' => Team::inRandomOrder()->first()->id ?? Team::factory(), 
            'points' => $this->faker->numberBetween(0, 100),
            'won' => $this->faker->numberBetween(0, 30),
            'lose' => $this->faker->numberBetween(0, 30),
            'draw' => $this->faker->numberBetween(0, 30),
            'goal_difference' => $this->faker->numberBetween(-30, 30),
            'goals_scored' => $this->faker->numberBetween(0, 100),
            'goals_conceded' => $this->faker->numberBetween(0, 100),
        ];
    }
}
