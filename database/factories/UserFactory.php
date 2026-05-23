<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'firstname' => fake()->firstName(),
            'surname' => fake()->lastName(),
            'middlename' => fake()->optional()->lastName(),
            'role' => 3, // Student by default
            'status' => 1,
            'email' => fake()->unique()->safeEmail(),
            'contact' => fake()->phoneNumber(),
            'photo' => 'student.jpg',
            'password' => Hash::make('password'),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 1,
        ]);
    }

    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 2,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 3,
        ]);
    }
}
