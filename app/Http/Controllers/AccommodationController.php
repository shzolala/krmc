<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(): View
    {
        $accommodations = Accommodation::query()
            ->with('destination')
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12);

        return view('accommodations.index', [
            'accommodations' => $accommodations,
            'mapMarkers' => Accommodation::query()
                ->withCoordinates()
                ->get()
                ->map(fn (Accommodation $item) => $item->toMapMarker(
                    'accommodation',
                    route('accommodations.show', $item),
                ))
                ->filter()
                ->values(),
        ]);
    }

    public function show(Accommodation $accommodation): View
    {
        $accommodation->load('destination');

        return view('accommodations.show', [
            'accommodation' => $accommodation,
            'mapMarkers' => collect([
                $accommodation->toMapMarker('accommodation', route('accommodations.show', $accommodation)),
            ])->filter()->values(),
        ]);
    }
}
