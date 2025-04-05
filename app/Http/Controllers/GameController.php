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

    public function storeResult(Request $request, Game $game)
    {
        $validated = $request->validate([
            'sets' => ['required', 'array', 'min:1', 'max:3'],
            'sets.*.first_team' => ['nullable', 'integer', 'min:0', 'max:7'],
            'sets.*.second_team' => ['nullable', 'integer', 'min:0', 'max:7'],
        ]);

        // Validate that the game doesn't already have a result
        if ($game->result()->exists()) {
            return back()->withErrors(['sets' => 'This game already has a result.']);
        }

        // Filter out any sets where both scores are null
        $validSets = array_filter($validated['sets'], function ($set) {
            return ! is_null($set['first_team']) || ! is_null($set['second_team']);
        });

        // Create the game result
        $game->result()->create([
            'sets' => array_values($validSets),
        ]);

        return redirect()->back();
    }

    public function updateResult(Request $request, Game $game)
    {
        $validated = $request->validate([
            'sets' => ['required', 'array', 'min:1', 'max:3'],
            'sets.*.first_team' => ['nullable', 'integer', 'min:0', 'max:7'],
            'sets.*.second_team' => ['nullable', 'integer', 'min:0', 'max:7'],
        ]);

        // Validate that the game has a result
        if (! $game->result()->exists()) {
            return back()->withErrors(['sets' => 'This game does not have a result to update.']);
        }

        // Filter out any sets where both scores are null
        $validSets = array_filter($validated['sets'], function ($set) {
            return ! is_null($set['first_team']) || ! is_null($set['second_team']);
        });

        // Update the game result
        $game->result()->update([
            'sets' => array_values($validSets),
        ]);

        return redirect()->back();
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return redirect()->back();
    }
}
