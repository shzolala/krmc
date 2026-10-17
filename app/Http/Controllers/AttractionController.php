<?php

namespace App\Http\Controllers;

use App\Enums\AttractionType;
use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttractionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $type = $request->string('type')->toString();
        $validType = AttractionType::tryFrom($type);

        $attractions = Attraction::query()
            ->with('mountainRegion')
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($validType, fn ($query) => $query->where('type', $validType->value))
            ->withCount('destinations')
            ->withReviewStats()
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('attractions.index', [
            'attractions' => $attractions,
            'search' => $search,
            'activeType' => $validType?->value,
            'mapMarkers' => Attraction::query()
                ->withCoordinates()
                ->get()
                ->map(fn (Attraction $item) => $item->toMapMarker(
                    'attraction',
                    route('attractions.show', $item),
                ))
                ->filter()
                ->values(),
        ]);
    }

    public function show(Attraction $attraction): View
    {
        $attraction->load([
            'mountainRegion',
            'destinations.mountainRegion',
        ]);

        $mapMarkers = collect([
            $attraction->toMapMarker('attraction', route('attractions.show', $attraction)),
        ])
            ->merge(
                $attraction->destinations->map(
                    fn ($item) => $item->toMapMarker('destination', route('destinations.show', $item))
                )
            )
            ->filter()
            ->values();

        return view('attractions.show', [
            'attraction' => $attraction,
            'mapMarkers' => $mapMarkers,
        ]);
    }
}
