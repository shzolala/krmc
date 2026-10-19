<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $clubs = Club::query()
            ->withReviewStats()
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('manager', 'like', "%{$search}%");
            }))
            ->when($request->boolean('verified'), fn ($query) => $query->where('is_verified', true))
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')->toString()))
            ->when($request->boolean('has_programs'), fn ($query) => $query->whereHas('programs', fn ($programs) => $programs->active()))
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderByDesc('member_count')
            ->paginate(12)
            ->withQueryString();

        return view('clubs.index', [
            'clubs' => $clubs,
            'cities' => Club::query()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city', 'city'),
        ]);
    }

    public function show(Club $club): View
    {
        $club->load([
            'coachProfiles.user',
            'programs' => fn ($query) => $query
                ->with(['destination', 'leader.user'])
                ->active()
                ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 else 2 end")
                ->orderBy('starts_at')
                ->limit(8),
            'programReports' => fn ($query) => $query->latest('published_at')->limit(6),
        ]);

        return view('clubs.show', [
            'club' => $club,
        ]);
    }
}
