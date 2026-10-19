<?php

namespace App\Http\Controllers;

use App\Enums\DifficultyLevel;
use App\Enums\ProgramStatus;
use App\Models\Club;
use App\Models\MountainRegion;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('q')->trim()->toString();
        $difficulty = DifficultyLevel::tryFrom($request->string('difficulty')->toString());
        $region = $request->string('region')->toString();
        $club = $request->string('club')->toString();
        $onlyWithCapacity = $request->boolean('available');

        $programs = Program::query()
            ->with(['destination', 'club', 'leader.user'])
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when(
                $status === ProgramStatus::Ongoing->value,
                fn ($query) => $query->ongoing(),
                fn ($query) => $query->when(
                    in_array($status, array_column(ProgramStatus::cases(), 'value'), true),
                    fn ($statusQuery) => $statusQuery->where('status', $status),
                    fn ($defaultQuery) => $defaultQuery->active(),
                ),
            )
            ->when($difficulty, fn ($query) => $query->whereHas(
                'destination',
                fn ($destinationQuery) => $difficulty->applyTo($destinationQuery),
            ))
            ->when($region !== '', fn ($query) => $query->whereHas(
                'destination.mountainRegion',
                fn ($regionQuery) => $regionQuery->where('slug', $region),
            ))
            ->when($club !== '', fn ($query) => $query->whereHas('club', fn ($clubQuery) => $clubQuery->where('slug', $club)))
            ->when($onlyWithCapacity, fn ($query) => $query->where(
                fn ($capacityQuery) => $capacityQuery->whereNull('capacity')->orWhereColumn('registered_count', '<', 'capacity'),
            ))
            ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 else 2 end")
            ->orderBy('starts_at')
            ->paginate(12)
            ->withQueryString();

        return view('programs.index', [
            'programs' => $programs,
            'regions' => MountainRegion::query()
                ->whereHas('destinations.programs')
                ->orderBy('name')
                ->get(['name', 'slug']),
            'clubs' => Club::query()
                ->whereHas('programs')
                ->orderBy('name')
                ->get(['name', 'slug']),
        ]);
    }

    public function show(Program $program): View
    {
        $program->load(['destination.mountainRegion', 'club', 'leader.user']);

        return view('programs.show', [
            'program' => $program,
        ]);
    }
}
