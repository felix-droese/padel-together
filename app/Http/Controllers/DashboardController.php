<?php

namespace App\Http\Controllers;

use App\DTOs\TGame;
use App\DTOs\TLocation;
use App\DTOs\TPlayer;
use App\Models\Game;
use App\Models\Location;
use App\Models\Player;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {

        $locations = Location::all()->map(fn ($location) => TLocation::from($location));
        $players = Player::all()->map(fn ($player) => TPlayer::from($player));
        $games = Game::with(['firstTeam.players', 'secondTeam.players', 'location'])->get()
            ->map(fn ($game) => TGame::from($game));

        $openGames = $games->filter(fn ($game) => $game->date > now());

        return Inertia::render('Dashboard', [
            'locations' => $locations,
            'players' => $players,
            'games' => $games,
            'openGames' => $openGames,
        ]);
    }
}
