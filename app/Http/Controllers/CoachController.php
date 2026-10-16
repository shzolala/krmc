<?php

namespace App\Http\Controllers;

use App\Models\CoachProfile;
use Illuminate\View\View;

class CoachController extends Controller
{
    public function index(): View
    {
        $coaches = CoachProfile::query()
            ->with(['user', 'club'])
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('slug')
            ->paginate(12);

        return view('coaches.index', [
            'coaches' => $coaches,
        ]);
    }

    public function show(CoachProfile $coach): View
    {
        $coach->load(['user', 'club', 'destinationGuides.destination']);

        return view('coaches.show', [
            'coach' => $coach,
        ]);
    }
}
