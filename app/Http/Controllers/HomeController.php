<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Attraction;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Models\Program;
use App\Models\ProgramReport;
use App\Models\Shelter;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredClubs = Club::query()
            ->withReviewStats()
            ->withCount(['coachProfiles', 'programs'])
            ->with(['programs' => fn ($query) => $query->active()])
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
            ->with('mountainRegion')
            ->withCount('guides')
            ->withReviewStats()
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->orderByDesc('view_count')
            ->limit(8)
            ->get();

        $featuredRegions = MountainRegion::query()
            ->withCount(['destinations', 'attractions'])
            ->orderByDesc('is_featured')
            ->orderByDesc('highest_elevation')
            ->limit(4)
            ->get();

        $featuredAttractions = Attraction::query()
            ->with('mountainRegion')
            ->withReviewStats()
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->limit(4)
            ->get();

        $featuredCoaches = CoachProfile::query()
            ->with(['user', 'club'])
            ->withReviewStats()
            ->where(function ($query): void {
                $query->where('is_featured', true)
                    ->orWhere('is_premium', true);
            })
            ->orderByDesc('is_premium')
            ->limit(4)
            ->get();

        $upcomingPrograms = Program::query()
            ->with(['destination', 'club'])
            ->active()
            ->orderBy('starts_at')
            ->limit(3)
            ->get();

        $latestReports = ProgramReport::query()
            ->with(['destination', 'club'])
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->limit(3)
            ->get();

        $featuredAccommodations = Accommodation::query()
            ->with('destination')
            ->withReviewStats()
            ->where(function ($query): void {
                $query->where('is_featured', true)
                    ->orWhere('is_premium', true);
            })
            ->orderByDesc('is_premium')
            ->limit(4)
            ->get();

        $shelters = Shelter::query()
            ->with('destination')
            ->withReviewStats()
            ->orderByDesc('is_featured')
            ->orderByDesc('elevation')
            ->limit(6)
            ->get();

        return view('home', [
            'regionOptions' => MountainRegion::query()->orderBy('name')->get(['name', 'slug']),
            'stats' => [
                'clubs' => Club::query()->whereNotNull('farakouh_id')->count(),
                'coaches' => CoachProfile::query()->whereNotNull('farakouh_id')->count(),
                'programs' => Program::query()->ongoing()->count(),
                'attractions' => Attraction::query()->count(),
                'shelters' => Shelter::query()->count(),
            ],
            'featuredClubs' => $featuredClubs,
            'popularDestinations' => $popularDestinations,
            'featuredRegions' => $featuredRegions,
            'featuredAttractions' => $featuredAttractions,
            'featuredCoaches' => $featuredCoaches,
            'upcomingPrograms' => $upcomingPrograms,
            'latestReports' => $latestReports,
            'featuredAccommodations' => $featuredAccommodations,
            'shelters' => $shelters,
        ]);
    }
}
