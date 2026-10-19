<?php

namespace App\Http\Controllers\Panel;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Shop;
use App\Support\PanelDashboard;
use App\Support\PanelRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function __invoke(Request $request, ?Club $club = null, ?Shop $shop = null): View|RedirectResponse
    {
        $user = $request->user();
        $role = UserRole::from((string) $request->route('section'));
        $entity = $club ?? $shop;

        if ($entity !== null) {
            Gate::authorize('update', $entity);
        } elseif (in_array($role, [UserRole::Club, UserRole::Shop], true)) {
            $first = $role === UserRole::Club ? $user->ownedClubs->first() : $user->shops->first();

            return redirect()->route($role->dashboardRoute(), $first);
        }

        $user->loadMissing('coachProfile.club');

        return view('panel.role-dashboard', [
            'user' => $user,
            'panel' => PanelRegistry::forRole($role),
            'role' => $role,
            'dashboard' => PanelDashboard::for($user, $role, $entity),
        ]);
    }
}
