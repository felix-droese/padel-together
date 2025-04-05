<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'second_team_player_id' => ['required', Rule::exists('players', 'id')],
        ]);

        // Get the authenticated user's player
        $userPlayer = Player::where('user_id', Auth::id())->firstOrFail();

        // Create first team with the authenticated user's player
        $firstTeam = Team::create();
        $firstTeam->players()->attach($userPlayer->id);

        // Create second team with the selected player
        $secondTeam = Team::create();
        $secondTeam->players()->attach($validated['second_team_player_id']);

        // Create the game
        Game::create([
            'first_team_id' => $firstTeam->id,
            'second_team_id' => $secondTeam->id,
            'date' => $validated['date'],
            'location_id' => $validated['location_id'],
        ]);

        return redirect()->back();
    }
}
