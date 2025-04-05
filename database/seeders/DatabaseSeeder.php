<?php

namespace Database\Seeders;

use App\Models\EloChange;
use App\Models\Game;
use App\Models\Location;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Services\EloService;
use Illuminate\Database\Seeder;
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

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'droese.felix@gmail.com'],
            [
                'name' => 'Felix Droese',
                'password' => Hash::make('1234'),
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create players
        $players = collect([
            ['name' => 'Felix Droese', 'email' => 'droese.felix@gmail.com', 'is_admin' => true],
            ['name' => 'Stefan Woischner', 'email' => 'stefan@example.com'],
            ['name' => 'Tobias Kaiser', 'email' => 'tobias@example.com'],
            ['name' => 'Yannik Hansen', 'email' => 'yannik@example.com', 'is_admin' => true],
            ['name' => 'Sascha Baumann', 'email' => 'sascha@example.com'],
            ['name' => 'Simon Meißner', 'email' => 'simon@example.com'],
            ['name' => 'Paul Sochiera', 'email' => 'paul@example.com'],
        ]);

        $createdPlayers = $players->map(function ($playerData) use ($adminRole) {
            $user = User::firstOrCreate(
                ['email' => $playerData['email']],
                [
                    'name' => $playerData['name'],
                    'password' => Hash::make('1234'),
                ]
            );

            if ($playerData['is_admin'] ?? false) {
                $user->assignRole($adminRole);
            }

            return Player::create([
                'user_id' => $user->id,
                'first_name' => explode(' ', $playerData['name'])[0],
                'last_name' => explode(' ', $playerData['name'])[1],
                'elo' => 1500, // Base ELO rating
            ]);
        });

        // Create locations
        $locations = collect([
            ['name' => 'Rummenigge'],
            ['name' => 'TC Union'],
            ['name' => 'Padel Club Hamburg'],
        ])->map(fn ($location) => Location::create($location));

        // Create some initial games with results
        $this->createGameWithResult(
            $createdPlayers[0], // Felix
            $createdPlayers[1], // Stefan
            $createdPlayers[2], // Tobias
            $createdPlayers[3], // Yannik
            $locations[0], // Rummenigge
            now()->subDays(7),
            [[6, 4], [6, 3]] // Felix/Stefan win
        );

        $this->createGameWithResult(
            $createdPlayers[0], // Felix
            $createdPlayers[3], // Yannik
            $createdPlayers[1], // Stefan
            $createdPlayers[2], // Tobias
            $locations[1], // TC Union
            now()->subDays(5),
            [[6, 2], [6, 4]] // Felix/Yannik win
        );

        $this->createGameWithResult(
            $createdPlayers[4], // Sascha
            $createdPlayers[5], // Simon
            $createdPlayers[6], // Paul
            $createdPlayers[0], // Felix
            $locations[2], // Padel Club Hamburg
            now()->subDays(3),
            [[4, 6], [6, 4], [6, 2]] // Sascha/Simon win
        );

        // Create more games to establish realistic ELO ratings
        $this->createGameWithResult(
            $createdPlayers[2], // Tobias
            $createdPlayers[3], // Yannik
            $createdPlayers[4], // Sascha
            $createdPlayers[5], // Simon
            $locations[0], // Rummenigge
            now()->subDays(2),
            [[6, 3], [6, 4]] // Tobias/Yannik win
        );

        $this->createGameWithResult(
            $createdPlayers[1], // Stefan
            $createdPlayers[6], // Paul
            $createdPlayers[0], // Felix
            $createdPlayers[3], // Yannik
            $locations[1], // TC Union
            now()->subDays(1),
            [[6, 4], [7, 5]] // Stefan/Paul win
        );

        $this->createGameWithResult(
            $createdPlayers[3], // Yannik
            $createdPlayers[4], // Sascha
            $createdPlayers[1], // Stefan
            $createdPlayers[5], // Simon
            $locations[2], // Padel Club Hamburg
            now()->subHours(12),
            [[6, 2], [6, 3]] // Yannik/Sascha win
        );
    }

    private function createGameWithResult(
        Player $player1,
        Player $player2,
        Player $player3,
        Player $player4,
        Location $location,
        $date,
        array $sets
    ): void {
        // Create teams
        $team1 = Team::create();
        $team1->players()->attach([$player1->id, $player2->id]);

        $team2 = Team::create();
        $team2->players()->attach([$player3->id, $player4->id]);

        // Create game
        $game = Game::create([
            'first_team_id' => $team1->id,
            'second_team_id' => $team2->id,
            'location_id' => $location->id,
            'date' => $date,
        ]);

        // Create result
        $game->result()->create([
            'sets' => array_map(fn ($set) => ['first_team' => $set[0], 'second_team' => $set[1]], $sets),
        ]);

        // Calculate and apply ELO changes
        $newRatings = EloService::calculateNewRatings($player1, $player2, $player3, $player4);

        // Store ELO changes for each player
        foreach ($newRatings as $playerId => $newRating) {
            $player = Player::find($playerId);
            $change = $newRating - $player->elo;

            EloChange::create([
                'game_id' => $game->id,
                'player_id' => $playerId,
                'change' => $change,
            ]);

            $player->update(['elo' => $newRating]);
        }
    }
}
