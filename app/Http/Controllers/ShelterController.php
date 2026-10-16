<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\View\View;

class ShelterController extends Controller
{
    public function index(): View
    {
        $shelters = Shelter::query()
            ->with('destination')
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->paginate(12);

        return view('shelters.index', [
            'shelters' => $shelters,
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
