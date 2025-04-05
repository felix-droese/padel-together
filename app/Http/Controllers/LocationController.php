<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        return Inertia::render('locations/Index', [
            'locations' => Location::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('locations/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('locations')],
        ]);

        Location::create($validated);

        return redirect()->back();
    }
}
