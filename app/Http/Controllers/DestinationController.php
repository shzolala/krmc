<?php

namespace App\Http\Controllers;

use App\Enums\DestinationCategory;
use App\Models\Destination;
use App\Models\MountainRegion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->string('category')->toString();
        $search = $request->string('q')->trim()->toString();

        $destinations = Destination::query()
            ->with('mountainRegion')
            ->withCount('guides')
            ->withReviewStats()
            ->when(
                in_array($category, array_column(DestinationCategory::cases(), 'value'), true),
                fn ($query) => $query->where('category', $category),
            )
            ->when(
                $search !== '',
                fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%");
                }),
            )
            ->when(
                $request->filled('region'),
                fn ($query) => $query->whereHas(
                    'mountainRegion',
                    fn ($regionQuery) => $regionQuery->where('slug', $request->string('region')->toString()),
                ),
            )
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('destinations.index', [
            'destinations' => $destinations,
            'activeCategory' => $category,
            'activeRegion' => $request->string('region')->toString(),
            'search' => $search,
            'regions' => MountainRegion::query()->orderBy('name')->get(['name', 'slug']),
            'mapMarkers' => Destination::query()
                ->withCoordinates()
                ->when(
                    in_array($category, array_column(DestinationCategory::cases(), 'value'), true),
                    fn ($query) => $query->where('category', $category),
                )
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
            'mountainRegion',
            'guides.club',
            'guides.coachProfile.user',
            'programs' => fn ($query) => $query
                ->with(['club', 'leader.user'])
                ->active()
                ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 else 2 end")
                ->orderBy('starts_at')
                ->limit(8),
            'programReports' => fn ($query) => $query->latest('published_at')->limit(6),
            'accommodations',
            'shelters',
            'attractions.mountainRegion',
            'relatedDestinations.mountainRegion',
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
            ->merge(
                $destination->attractions
                    ->map(fn ($item) => $item->toMapMarker('attraction', route('attractions.show', $item)))
            )
            ->merge(
                $destination->relatedDestinations
                    ->map(fn ($item) => $item->toMapMarker('destination', route('destinations.show', $item)))
            )
            ->filter()
            ->values();

        return view('destinations.show', [
            'destination' => $destination,
            'mapMarkers' => $mapMarkers,
        ]);
    }
}
