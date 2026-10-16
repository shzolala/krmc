<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\ProgramReport;
use App\Models\Shelter;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredClubs = Club::query()
            ->where(function ($query): void {
                $query->where('is_featured', true)
                    ->orWhere('is_premium', true);
            })
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderByDesc('member_count')
            ->limit(4)
            ->get();

        $popularDestinations = Destination::query()
            ->withCount('guides')
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->limit(8)
            ->get();

        $featuredCoaches = CoachProfile::query()
            ->with(['user', 'club'])
            ->where(function ($query): void {
                $query->where('is_featured', true)
                    ->orWhere('is_premium', true);
            })
            ->orderByDesc('is_premium')
            ->limit(4)
            ->get();

        $latestReports = ProgramReport::query()
            ->with(['destination', 'club'])
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(3)
            ->get();

        $featuredAccommodations = Accommodation::query()
            ->with('destination')
            ->where(function ($query): void {
                $query->where('is_featured', true)
                    ->orWhere('is_premium', true);
            })
            ->orderByDesc('is_premium')
            ->limit(4)
            ->get();

        $shelters = Shelter::query()
            ->with('destination')
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->limit(6)
            ->get();

        return view('home', [
            'stats' => [
                'clubs' => Club::query()->count(),
                'coaches' => CoachProfile::query()->count(),
                'destinations' => Destination::query()->count(),
                'shelters' => Shelter::query()->count(),
            ],
            'featuredClubs' => $featuredClubs,
            'popularDestinations' => $popularDestinations,
            'featuredCoaches' => $featuredCoaches,
            'latestReports' => $latestReports,
            'featuredAccommodations' => $featuredAccommodations,
            'shelters' => $shelters,
        ]);
    }
}
