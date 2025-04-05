<?php

namespace App\Http\Controllers;

use App\DTOs\TLocation;
use App\Models\Location;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = Location::all()->map(fn ($location) => TLocation::from($location));

        return Inertia::render('Dashboard', [
            'locations' => $locations,
        ]);
    }
}
