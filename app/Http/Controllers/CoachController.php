<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $role = $request->string('role')->toString();

        $coaches = CoachProfile::query()
            ->with(['user', 'club'])
            ->withReviewStats()
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->where('specialty', 'like', "%{$search}%")
                    ->orWhere('headline', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            }))
            ->when($role === 'leader', fn ($query) => $query->where('is_leader', true))
            ->when($role === 'coach', fn ($query) => $query->where('is_leader', false))
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('slug')
            ->paginate(12)
            ->withQueryString();

        return view('coaches.index', [
            'coaches' => $coaches,
            'search' => $search,
            'activeRole' => $role,
        ]);
    }

    public function show(CoachProfile $coach): View
    {
        $coach->load([
            'user',
            'club',
            'destinationGuides.destination',
            'ledPrograms' => fn ($query) => $query
                ->with(['destination', 'club'])
                ->active()
                ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 else 2 end")
                ->orderBy('starts_at')
                ->limit(8),
        ]);

        return view('coaches.show', [
            'coach' => $coach,
        ]);
    }
}
