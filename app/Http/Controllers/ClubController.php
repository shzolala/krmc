<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(): View
    {
        $clubs = Club::query()
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderByDesc('member_count')
            ->paginate(12);

        return view('clubs.index', [
            'clubs' => $clubs,
        ]);
    }

    public function show(Club $club): View
    {
        $club->load(['coachProfiles.user', 'programReports' => fn ($query) => $query->latest('published_at')->limit(6)]);

        return view('clubs.show', [
            'club' => $club,
        ]);
    }
}
