<?php

namespace Database\Factories;

use App\Models\IncomeGroup;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->text(100),
            'capacity' => $this->faker->numberBetween(10, 40),
        ];
    }

    public function withFees(): static
    {
        return $this->hasAttached(IncomeGroup::all(), ['fee' => 200], 'fees');
    }

    public function withLessons(): static
    {
        return $this->has(Lesson::factory(10));
    }
}
