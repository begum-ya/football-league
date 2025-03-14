<?php

namespace Database\Factories;

use App\Models\Week;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeekFactory extends Factory
{
    protected $model = Week::class;

    public function definition()
    {
        return [
            'week' => $this->faker->unique()->numberBetween(1, 38),
        ];
    }
}
