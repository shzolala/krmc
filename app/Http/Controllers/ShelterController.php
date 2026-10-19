<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShelterController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $shelters = Shelter::query()
            ->with('destination')
            ->withReviewStats()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($request->filled('destination'), fn ($query) => $query->whereHas('destination', fn ($destination) => $destination->where('slug', $request->string('destination')->toString())))
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->paginate(12)
            ->withQueryString();

        return view('shelters.index', [
            'shelters' => $shelters,
            'destinations' => Destination::query()->whereHas('shelters')->orderBy('name')->pluck('name', 'slug'),
            'mapMarkers' => Shelter::query()
                ->withCoordinates()
                ->get()
                ->map(fn (Shelter $item) => $item->toMapMarker(
                    'shelter',
                    route('shelters.show', $item),
                ))
                ->filter()
                ->values(),
        ]);
    }

    public function show(Shelter $shelter): View
    {
        $shelter->load('destination');

        return view('shelters.show', [
            'shelter' => $shelter,
            'mapMarkers' => collect([
                $shelter->toMapMarker('shelter', route('shelters.show', $shelter)),
            ])->filter()->values(),
        ]);
    }
}
