<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = \App\Models\Answer::class;

    public function definition(): array
    {
        return [
            'student_id' => 1,
            'examination_id' => 1,
            'question_id' => 1,
            'student_answer' => fake()->sentence(2),
            'status' => 1,
        ];
    }
}
