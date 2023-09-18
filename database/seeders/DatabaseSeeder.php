<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        DB::table('teams')->insert([
            'name' => 'Basic Group',
            'description' => 'Basic group, which meets every Thursday',
        ]);

        DB::table('teams')->insert([
            'name' => 'Intensive Group',
            'description' => 'Intensive group, which meets every Tuesday',
        ]);

        foreach(\App\Models\User::all() as $u) {
            $u->teams()->attach(\App\Models\Team::first());
        }

        foreach(\App\Models\User::find([1, 2, 3]) as $u) {
            $u->teams()->attach(\App\Models\Team::find([2]));
        }
    }
}
