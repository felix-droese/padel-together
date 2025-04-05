<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Location;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $user = User::firstOrCreate(
            ['email' => 'droese.felix@gmail.com'],
            [
                'name' => 'Felix Droese',
                'password' => Hash::make('1234'),
            ]
        );

        $user->assignRole($adminRole);

        Player::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'Felix',
            'last_name' => 'Droese',
        ]);

        $users = collect([
            ['name' => 'Stefan Woischner', 'email' => 'stefan@example.com'],
            ['name' => 'Tobias Kaiser', 'email' => 'tobias@example.com'],
            ['name' => 'Yannik Hansen', 'email' => 'yannik@example.com'],
            ['name' => 'Sascha Baumann', 'email' => 'sascha@example.com'],
            ['name' => 'Simon Meißner', 'email' => 'simon@example.com'],
            ['name' => 'Paul Sochiera', 'email' => 'paul@example.com'],
        ]);

        $users->each(function ($userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('1234'),
                ]
            );

            Player::factory()->create([
                'user_id' => $user->id,
                'first_name' => explode(' ', $userData['name'])[0],
                'last_name' => explode(' ', $userData['name'])[1],
            ]);
        });

        $rummenigge = Location::create([
            'name' => 'Rummenigge',
        ]);

        $tcUnion = Location::create([
            'name' => 'TC Union',
        ]);

        $players = Player::all();
        $firstTeam = Team::factory()->create();
        $firstTeam->players()->attach([
            $players->random()->id,
            $players->random()->id,
        ]);

        $secondTeam = Team::factory()->create();
        $secondTeam->players()->attach([
            $players->random()->id,
            $players->random()->id,
        ]);

        Game::create([
            'first_team_id' => $firstTeam->id,
            'second_team_id' => $secondTeam->id,
            'location_id' => $rummenigge->id,
            'date' => now(),
        ]);

        Game::create([
            'first_team_id' => $firstTeam->id,
            'second_team_id' => $secondTeam->id,
            'location_id' => $tcUnion->id,
            'date' => now()->addDays(1)->setHours(14)->setMinutes(30),
        ]);
    }
}
