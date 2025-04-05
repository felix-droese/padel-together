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
            'first_team_players' => ['sometimes', 'array', 'min:1', 'max:2'],
            'first_team_players.*' => ['required', Rule::exists('players', 'id')],
            'second_team_players' => ['sometimes', 'array', 'min:1', 'max:2'],
            'second_team_players.*' => ['required', Rule::exists('players', 'id')],
        ]);

        // Get the authenticated user's player
        $userPlayer = Player::where('user_id', Auth::id())->firstOrFail();

        // Create first team
        $firstTeam = Team::create();
        if (isset($validated['first_team_players'])) {
            $firstTeam->players()->attach($validated['first_team_players']);
        } else {
            $firstTeam->players()->attach($userPlayer->id);
        }

        // Create second team if players are provided
        $secondTeam = null;
        if (isset($validated['second_team_players'])) {
            $secondTeam = Team::create();
            $secondTeam->players()->attach($validated['second_team_players']);
        }

        // Create the game
        Game::create([
            'first_team_id' => $firstTeam->id,
            'second_team_id' => $secondTeam?->id,
            'date' => $validated['date'],
            'location_id' => $validated['location_id'],
        ]);

        return redirect()->back();
    }
}
