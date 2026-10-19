<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\FundIncome;
use App\Models\MembershipPlan;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(): View
    {
        $plans = MembershipPlan::query()->active()->orderBy('level')->orderBy('price')->get();

        $groups = collect(UserRole::cases())
            ->reject(fn (UserRole $role): bool => $role === UserRole::Admin)
            ->map(fn (UserRole $role): array => [
                'role' => $role,
                'plans' => $plans->filter(fn (MembershipPlan $plan): bool => $plan->role === null || $plan->role === $role)->values(),
            ])
            ->filter(fn (array $group): bool => $group['plans']->isNotEmpty())
            ->values();

        $sponsors = Sponsor::query()->visible()->get(['id']);

        return view('pricing', [
            'groups' => $groups,
            'packages' => SponsorshipPackage::query()->active()->orderBy('sort_order')->orderBy('min_amount')->get(),
            'sponsorCount' => $sponsors->count(),
            'sponsorSupport' => (int) FundIncome::query()->whereIn('sponsor_id', $sponsors->pluck('id'))->sum('amount'),
        ]);
    }
}
