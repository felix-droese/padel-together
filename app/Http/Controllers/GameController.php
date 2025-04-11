<?php

namespace App\Http\Controllers;

use App\DTOs\TEloChange;
use App\DTOs\TGame;
use App\DTOs\TGamePayment;
use App\DTOs\TLocation;
use App\DTOs\TPlayer;
use App\DTOs\TTeam;
use App\DTOs\TUser;
use App\Models\EloChange;
use App\Models\Game;
use App\Models\GamePayment;
use App\Models\Location;
use App\Models\Player;
use App\Models\Team;
use App\Services\EloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index()
    {
        $locations = Location::all()->map(fn ($location) => TLocation::from($location));
        $players = Player::all()->map(fn ($player) => TPlayer::from($player));
        $games = Game::with(['firstTeam.players', 'secondTeam.players', 'location', 'result', 'eloChanges', 'payments.user', 'payer'])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($game) {
                $tGame = TGame::from($game);
                $tGame->winning_team = $game->winning_team ? TTeam::from($game->winning_team) : null;
                $tGame->elo_changes = $game->eloChanges->map(fn ($change) => TEloChange::from($change));
                $tGame->payments = $game->payments->map(fn ($payment) => TGamePayment::from($payment));
                $tGame->payer = $game->payer ? TUser::from($game->payer) : null;
                // dd($tGame->payer);

                return $tGame;
            });

        return Inertia::render('games/Index', [
            'locations' => $locations,
            'players' => $players,
            'games' => $games,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'location_id' => ['required', Rule::exists('locations', 'id')],
            'first_team_players' => ['sometimes', 'array', 'min:1', 'max:2'],
            'first_team_players.*' => ['nullable', Rule::exists('players', 'id')],
            'second_team_players' => ['sometimes', 'array', 'min:1', 'max:2'],
            'second_team_players.*' => ['nullable', Rule::exists('players', 'id')],
            'price_in_cent' => ['required', 'integer', 'min:0'],
            'payer_id' => ['required', 'exists:users,id'],
        ]);

        // Get the authenticated user's player
        $userPlayer = Player::where('user_id', Auth::id())->firstOrFail();

        // Create first team
        $firstTeam = Team::create();
        $firstTeamPlayers = array_filter($validated['first_team_players'] ?? [], fn ($id) => $id !== null);
        if (empty($firstTeamPlayers)) {
            $firstTeamPlayers = [$userPlayer->id];
        }
        $firstTeam->players()->attach($firstTeamPlayers);

        // Create second team if players are provided
        $secondTeam = null;
        if (isset($validated['second_team_players'])) {
            $secondTeam = Team::create();
            $secondTeamPlayers = array_filter($validated['second_team_players'], fn ($id) => $id !== null);
            if (! empty($secondTeamPlayers)) {
                $secondTeam->players()->attach($secondTeamPlayers);
            }
        }

        // Create the game
        $game = Game::create([
            'first_team_id' => $firstTeam->id,
            'second_team_id' => $secondTeam?->id,
            'date' => $validated['date'],
            'location_id' => $validated['location_id'],
            'price_in_cent' => $validated['price_in_cent'],
            'payer_id' => $validated['payer_id'],
        ]);

        // Create payment records for each player
        $playerAmount = ceil($validated['price_in_cent'] / 4); // Divide by 4 and round up
        $allPlayers = array_merge($firstTeamPlayers, $secondTeamPlayers ?? []);

        foreach ($allPlayers as $playerId) {
            $player = Player::find($playerId);

            if ($player->user_id === $validated['payer_id']) {
                continue;
            }

            GamePayment::create([
                'game_id' => $game->id,
                'user_id' => $player->user_id,
                'amount_in_cent' => $playerAmount,
                'status' => 'pending',
            ]);
        }

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

        // Update ELO ratings if there's a winning team
        if ($game->winning_team) {
            $winningTeam = $game->winning_team;
            $losingTeam = $winningTeam->id === $game->first_team_id ? $game->secondTeam : $game->firstTeam;

            if ($winningTeam && $losingTeam) {
                $newRatings = EloService::calculateNewRatings(
                    $winningTeam->players[0],
                    $winningTeam->players[1],
                    $losingTeam->players[0],
                    $losingTeam->players[1]
                );

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

        // Delete existing ELO changes for this game
        EloChange::where('game_id', $game->id)->delete();

        // Update ELO ratings if there's a winning team
        if ($game->winning_team) {
            $winningTeam = $game->winning_team;
            $losingTeam = $winningTeam->id === $game->first_team_id ? $game->secondTeam : $game->firstTeam;

            if ($winningTeam && $losingTeam) {
                $newRatings = EloService::calculateNewRatings(
                    $winningTeam->players[0],
                    $winningTeam->players[1],
                    $losingTeam->players[0],
                    $losingTeam->players[1]
                );

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

        return redirect()->back();
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return redirect()->back();
    }
}
