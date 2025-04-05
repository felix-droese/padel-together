<?php

namespace App\Http\Controllers;

use App\DTOs\TLocation;
use App\DTOs\TPlayer;
use App\Models\Location;
use App\Models\Player;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = Location::all()->map(fn ($location) => TLocation::from($location));
        $players = Player::all()->map(fn ($player) => TPlayer::from($player));

        return Inertia::render('Dashboard', [
            'locations' => $locations,
            'players' => $players,
        ]);
    }
}
