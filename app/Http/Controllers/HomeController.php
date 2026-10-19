<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Attraction;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\MountainRegion;
use App\Models\Program;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Sponsor;
use App\Models\SupportProject;
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
            ->orderByRaw("case status when 'ongoing' then 0 else 1 end")
            ->orderBy('starts_at')
            ->limit(4)
            ->get();

        $heroSlides = HeroSlide::query()
            ->active()
            ->with(['program', 'destination', 'mountainRegion'])
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

        $sponsors = Sponsor::query()
            ->visible()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sponsorProjects = SupportProject::query()
            ->withFunding()
            ->with(['incomes' => fn ($incomes) => $incomes->whereNotNull('sponsor_id')->with('sponsor:id,name,slug,logo_path,status')])
            ->orderByRaw("status = 'active' desc")
            ->orderByDesc('is_featured')
            ->latest('id')
            ->limit(6)
            ->get();

        $mapMarkers = Destination::query()
            ->withCoordinates()
            ->get()
            ->map(fn (Destination $destination) => $destination->toMapMarker('destination', route('destinations.show', $destination)))
            ->merge(
                Shelter::query()
                    ->withCoordinates()
                    ->get()
                    ->map(fn (Shelter $shelter) => $shelter->toMapMarker('shelter', route('shelters.show', $shelter))),
            )
            ->filter()
            ->values();

        return view('home', [
            'mapMarkers' => $mapMarkers,
            'heroSlides' => $heroSlides,
            'ongoingProgramsCount' => Program::query()->ongoing()->count(),
            'featuredClubs' => $featuredClubs,
            'featuredRegions' => $featuredRegions,
            'featuredAttractions' => $featuredAttractions,
            'featuredCoaches' => $featuredCoaches,
            'upcomingPrograms' => $upcomingPrograms,
            'latestReports' => $latestReports,
            'featuredAccommodations' => $featuredAccommodations,
            'shelters' => $shelters,
            'sponsors' => $sponsors,
            'sponsorProjects' => $sponsorProjects,
        ]);
    }
}
