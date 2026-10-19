<?php

namespace App\Http\Controllers;

use App\Enums\ProjectCategory;
use App\Models\SupportProject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $category = ProjectCategory::tryFrom((string) $request->query('category'));

        $projects = SupportProject::query()
            ->withFunding()
            ->with(['incomes' => fn ($incomes) => $incomes->whereNotNull('sponsor_id')->with('sponsor:id,name,slug,logo_path,status')])
            ->when($category, fn ($query) => $query->where('category', $category->value))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'category' => $category,
            'categories' => ProjectCategory::cases(),
            'counts' => SupportProject::query()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category'),
            'totalRaised' => $projects->sum(fn (SupportProject $project): int => $project->raisedTotal()),
            'totalBudget' => (int) $projects->sum('budget'),
        ]);
    }

    public function show(SupportProject $project): View
    {
        $detailed = SupportProject::query()->withFunding()->with('activities')->whereKey($project->id)->firstOrFail();

        return view('projects.show', [
            'project' => $detailed,
            'supporters' => $detailed->supporters(),
        ]);
    }
}
