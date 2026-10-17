<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Support\PanelDashboard;
use App\Support\PanelRegistry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $panel = PanelRegistry::forRole($user->role);

        $user->loadMissing(['primaryClub', 'coachProfile.club', 'shop']);

        return view('panel.role-dashboard', [
            'user' => $user,
            'panel' => $panel,
            'role' => $user->role,
            'dashboard' => PanelDashboard::for($user),
        ]);
    }
}
