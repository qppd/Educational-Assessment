<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Examination>
 */
class ExaminationFactory extends Factory
{
    protected $model = \App\Models\Examination::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'duration' => fake()->numberBetween(10, 120),
            'limit' => fake()->numberBetween(50, 200),
            'description' => fake()->paragraph(),
            'status' => 1,
            'examination_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'administrator_id' => 1,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2,
        ]);
    }
}
