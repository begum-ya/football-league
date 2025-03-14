<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Standing;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
        ];
    }

    public function configure()
    {
        return $this;
    }
}
