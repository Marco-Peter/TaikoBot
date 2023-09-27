<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        foreach(\App\Models\User::all() as $u) {
            $u->team = Team::first();
        }
    }
}
