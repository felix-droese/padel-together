<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Player;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('1234'),
        ]);

        Player::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $users = collect();
        for ($i = 0; $i < 4; $i++) {
            $users->push(User::factory()->create([
                'name' => fake()->name(),
                'email' => "user{$i}@example.com",
                'password' => Hash::make('1234'),
            ]));
        }

        $users->each(function ($user) {
            Player::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        Location::create([
            'name' => 'Rummenigge',
        ]);
    }
}
