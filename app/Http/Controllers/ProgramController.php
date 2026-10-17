<?php

namespace App\Http\Controllers;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('q')->trim()->toString();

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
            ->orderByRaw("case status when 'ongoing' then 0 when 'upcoming' then 1 else 2 end")
            ->orderBy('starts_at')
            ->paginate(12)
            ->withQueryString();

        return view('programs.index', [
            'programs' => $programs,
            'activeStatus' => $status,
            'search' => $search,
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
