<?php

namespace App\Http\Controllers;

use App\Models\FundIncome;
use App\Models\Sponsor;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(): View
    {
        $sponsors = Sponsor::query()
            ->visible()
            ->withSum('incomes', 'amount')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('sponsors.index', [
            'sponsors' => $sponsors,
            'totalSupport' => (int) FundIncome::query()->whereIn('sponsor_id', $sponsors->pluck('id'))->sum('amount'),
        ]);
    }

    public function show(Sponsor $sponsor): View
    {
        abort_unless($sponsor->status->value === 'active', 404);

        $sponsor->loadSum('incomes', 'amount')->load('package:id,name');

        return view('sponsors.show', [
            'sponsor' => $sponsor,
            'projects' => $sponsor->supportedProjects(),
            'others' => Sponsor::query()->visible()->whereKeyNot($sponsor->id)->orderByDesc('is_featured')->orderBy('sort_order')->limit(10)->get(),
        ]);
    }
}
