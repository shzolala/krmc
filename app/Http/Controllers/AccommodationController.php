<?php

namespace App\Http\Controllers;

use App\Enums\LodgingType;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $validType = LodgingType::tryFrom($request->string('type')->toString());

        $accommodations = Accommodation::query()
            ->with('destination')
            ->withReviewStats()
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($validType, fn ($query) => $query->where('type', $validType->value))
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')->toString()))
            ->when($request->boolean('verified'), fn ($query) => $query->where('is_verified', true))
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('accommodations.index', [
            'accommodations' => $accommodations,
            'cities' => Accommodation::query()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city', 'city'),
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
