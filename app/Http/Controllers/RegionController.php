<?php

namespace App\Http\Controllers;

use App\Enums\DestinationCategory;
use App\Enums\MountainRegionType;
use App\Models\MountainRegion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $regions = MountainRegion::query()
            ->withCount(['destinations', 'attractions'])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when(MountainRegionType::tryFrom($request->string('type')->toString()), fn ($query, MountainRegionType $type) => $query->where('type', $type->value))
            ->orderByDesc('is_featured')
            ->orderByDesc('highest_elevation')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('regions.index', [
            'regions' => $regions,
            'mapMarkers' => MountainRegion::query()
                ->withCoordinates()
                ->get()
                ->map(fn (MountainRegion $item) => $item->toMapMarker(
                    'region',
                    route('regions.show', $item),
                ))
                ->filter()
                ->values(),
        ]);
    }

    public function show(MountainRegion $region): View
    {
        $region->load([
            'destinations' => fn ($query) => $query->orderByDesc('elevation')->orderBy('name'),
            'attractions' => fn ($query) => $query->orderByDesc('is_featured')->orderBy('name'),
        ]);

        $mapMarkers = collect([
            $region->toMapMarker('region', route('regions.show', $region)),
        ])
            ->merge(
                $region->destinations->map(
                    fn ($item) => $item->toMapMarker('destination', route('destinations.show', $item))
                )
            )
            ->merge(
                $region->attractions->map(
                    fn ($item) => $item->toMapMarker('attraction', route('attractions.show', $item))
                )
            )
            ->filter()
            ->values();

        return view('regions.show', [
            'region' => $region,
            'peaks' => $region->destinations
                ->filter(fn ($item) => $item->category === DestinationCategory::Peak)
                ->values(),
            'natureSpots' => $region->destinations
                ->filter(fn ($item) => $item->category === DestinationCategory::NatureSpot)
                ->values(),
            'mapMarkers' => $mapMarkers,
        ]);
    }
}
