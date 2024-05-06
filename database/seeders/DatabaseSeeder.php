<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Team::factory()->create([
            'name' => 'Basic Group',
            'description' => 'Basic group, which meets every Thursday',
        ]);

        Team::factory()->create([
            'name' => 'Intensive Group',
            'description' => 'Intensive group, which meets every Tuesday',
        ]);

        User::factory(10)->create();

        User::factory(3)->create([
            'team_id' => 2,
        ]);

        User::factory()->create([
            'name' => 'Student Taikostarter',
            'email' => 'student@example.com',
            'role' => 'student',
            'profile_photo_path' => 'profile-photos/taiko-kun.png',
        ]);

        User::factory()->create([
            'name' => 'Teacher Taikootaku',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
            'profile_photo_path' => 'profile-photos/taiko-face.jpg',
        ]);

        User::factory()->create([
            'name' => 'Admin Taikobaka',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'profile_photo_path' => 'profile-photos/Taiko1_1300-auto.jpg',
        ]);

        Course::factory(5)->withLessons()->create();
    }
}
