<?php

namespace App\Http\Controllers;

use App\DTOs\TPlayer;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PlayersController extends Controller
{
    public function index()
    {
        $players = Player::all()->map(fn ($player) => TPlayer::from($player));

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
}
