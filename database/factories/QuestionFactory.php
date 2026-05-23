<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    protected $model = \App\Models\Question::class;

    public function definition(): array
    {
        $choices = [
            fake()->sentence(3),
            fake()->sentence(3),
            fake()->sentence(3),
            fake()->sentence(3),
        ];
        $correctIndex = fake()->numberBetween(0, 3);

        return [
            'examination_id' => 1,
            'question' => fake()->sentence(6) . '?',
            'type' => 0, // Multiple choice
            'choice_1' => $choices[0],
            'choice_2' => $choices[1],
            'choice_3' => $choices[2],
            'choice_4' => $choices[3],
            'answer' => $choices[$correctIndex],
            'status' => 1,
            'professor_id' => 1,
        ];
    }

    public function enumeration(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 1,
            'choice_1' => null,
            'choice_2' => null,
            'choice_3' => null,
            'choice_4' => null,
            'answer' => fake()->word(),
        ]);
    }

    public function fillInTheBlank(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 2,
            'choice_1' => null,
            'choice_2' => null,
            'choice_3' => null,
            'choice_4' => null,
            'answer' => fake()->word(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }
}
