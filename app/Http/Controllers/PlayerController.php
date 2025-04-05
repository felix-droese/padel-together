<?php

namespace App\Http\Controllers;

use App\DTOs\TPlayer;
use App\DTOs\TUser;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('user')->get()->map(fn ($player) => TPlayer::from([
            'id' => $player->id,
            'first_name' => $player->first_name,
            'last_name' => $player->last_name,
            'user' => $player->user ? TUser::from(['email' => $player->user->email]) : null,
        ]));

        return Inertia::render('players/Index', [
            'players' => $players,
        ]);
    }

    public function create()
    {
        return Inertia::render('players/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
        ]);

        $player = Player::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('players.index');
    }

    public function destroy(Player $player)
    {
        $player->delete();

        return redirect()->route('players.index');
    }
}
