<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::query()
            ->withCount('guides')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12);

        return view('destinations.index', [
            'destinations' => $destinations,
            'mapMarkers' => Destination::query()
                ->withCoordinates()
                ->get()
                ->map(fn (Destination $item) => $item->toMapMarker(
                    'destination',
                    route('destinations.show', $item),
                ))
                ->filter()
                ->values(),
        ]);
    }

    public function show(Destination $destination): View
    {
        $destination->load([
            'guides.club',
            'guides.coachProfile.user',
            'programReports' => fn ($query) => $query->latest('published_at')->limit(6),
            'accommodations',
            'shelters',
        ]);

        $mapMarkers = collect([
            $destination->toMapMarker('destination', route('destinations.show', $destination)),
        ])
            ->merge(
                $destination->accommodations
                    ->map(fn ($item) => $item->toMapMarker('accommodation', route('accommodations.show', $item)))
            )
            ->merge(
                $destination->shelters
                    ->map(fn ($item) => $item->toMapMarker('shelter', route('shelters.show', $item)))
            )
            ->filter()
            ->values();

        return view('destinations.show', [
            'destination' => $destination,
            'mapMarkers' => $mapMarkers,
        ]);
    }
}
