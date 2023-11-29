<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Team;
use App\Models\User;
use App\Models\Course;
use App\Models\IncomeGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        IncomeGroup::factory()->create([
            'name' => 'Full',
            'description' => 'Standard rate for fully employed members',
        ]);

        IncomeGroup::factory()->create([
            'name' => 'Student',
            'description' => 'Reduced rate for students and unemployed',
        ]);

        DB::table('teams')->insert([
            'name' => 'Basic Group',
            'description' => 'Basic group, which meets every Thursday',
        ]);

        DB::table('teams')->insert([
            'name' => 'Intensive Group',
            'description' => 'Intensive group, which meets every Tuesday',
        ]);

        User::factory(10)->create();

        User::factory(3)->create([
            'team_id' => 2,
        ]);

        User::factory()->create([
            'first_name' => 'Student',
            'last_name' => 'Taikostarter',
            'email' => 'student@example.com',
            'role' => 'student',
            'profile_photo_path' => 'profile-photos/taiko-kun.png',
        ]);

        User::factory()->create([
            'first_name' => 'Teacher',
            'last_name' => 'Taikootaku',
            'email' => 'teacher@example.com',
            'role' => 'teacher',
            'profile_photo_path' => 'profile-photos/taiko-face.jpg',
        ]);

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Taikobaka',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'profile_photo_path' => 'profile-photos/Taiko1_1300-auto.jpg',
        ]);

        Course::factory(5)->hasAttached(
            IncomeGroup::all(),
            ['fee' => 200]
        )
            ->withLessons()
            ->create();

        //Course::factory()->count(5)->withFees();
    }
}
