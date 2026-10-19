<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Support\MembershipSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $subscription = new MembershipSubscription($user);
        $plans = $this->availablePlans($request)->get();

        return view('panel.membership.index', [
            'plans' => $plans,
            'evaluations' => $plans->mapWithKeys(fn (MembershipPlan $plan): array => [$plan->id => $subscription->evaluate($plan)]),
            'activeDue' => $subscription->activeDue(),
            'openDue' => $subscription->openDue(),
            'credit' => $subscription->upgradeCredit(),
            'floorLevel' => $subscription->floorLevel(),
            'badgePlan' => $user->currentMembershipPlan(),
            'termOptions' => $plans->flatMap(fn (MembershipPlan $plan): array => $plan->offeredTerms())->unique()->sort()->values(),
        ]);
    }

    public function choose(Request $request, MembershipPlan $plan): RedirectResponse
    {
        abort_unless($this->availablePlans($request)->whereKey($plan->id)->exists(), 404);

        $validated = $request->validate(['months' => ['required', 'integer', Rule::in(array_keys(MembershipPlan::TERM_DAYS))]]);

        $result = (new MembershipSubscription($request->user()))->choose($plan, (int) $validated['months']);

        return $result['ok']
            ? redirect()->route('panel.finance')->with('status', $result['message'])
            : back()->with('status', $result['message']);
    }

    private function availablePlans(Request $request): Builder
    {
        return MembershipPlan::query()
            ->active()
            ->availableFor($request->user())
            ->orderBy('level')
            ->orderBy('price');
    }
}
