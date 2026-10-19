<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Support\PanelDashboard;
use App\Support\PanelSections;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The landing page of every non-admin account: one card per section the person can manage, so someone who
 * runs a club, a shop and a coach profile sees all of them after a single login.
 */
class HomeController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('panel.admin.dashboard');
        }

        $user->loadMissing('coachProfile.club');

        $sections = PanelSections::for($user);

        if ($sections->count() === 1) {
            return redirect()->to($sections->first()['dashboard']);
        }

        return view('panel.home', [
            'user' => $user,
            'sections' => $sections->map(fn (array $section): array => [
                ...$section,
                'dashboard_data' => PanelDashboard::for($user, $section['role'], $section['entity']),
            ]),
        ]);
    }
}
