<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    protected $model = \App\Models\Result::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'examination_id' => 1,
            'score' => fake()->numberBetween(0, 50),
            'remarks' => fake()->optional()->sentence(),
            'status' => 1,
        ];
    }
}
