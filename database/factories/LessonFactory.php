<?php

namespace Database\Factories;

use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startdate = fake()->dateTime();
        return [
            'title' => fake()->sentence(2),
            'start' => $startdate,
            'finish' => (clone $startdate)->add(new DateInterval("PT2H")),
            'notes' => fake()->text(1000),
        ];
    }
}
