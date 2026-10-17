<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PanelRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ImpersonationController extends Controller
{
    public const SESSION_KEY = 'impersonator_id';

    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        if ($user->is($admin) || $user->isAdmin()) {
            return back()->with('error', 'امکان ورود به‌جای این حساب وجود ندارد.');
        }

        $request->session()->put(self::SESSION_KEY, $admin->id);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route(PanelRegistry::homeRouteFor($user))
            ->with('status', 'اکنون به‌عنوان «'.$user->name.'» وارد شده‌اید.');
    }

    /**
     * Dev-only quick switch between the seeded demo accounts, available to admins
     * (including while already viewing another role).
     */
    public function switchDemo(Request $request): RedirectResponse
    {
        abort_unless(PanelRegistry::switcherEnabled(), 404);

        $adminId = $request->session()->get(self::SESSION_KEY, $request->user()->id);
        $admin = User::query()->find($adminId);

        abort_unless($admin !== null && $admin->isAdmin(), 403);

        $validated = $request->validate([
            'mobile' => ['required', 'string', Rule::in(PanelRegistry::demoAccounts()->pluck('mobile')->all())],
        ]);

        $target = User::query()->where('mobile', $validated['mobile'])->firstOrFail();

        if ($target->is($admin)) {
            return $this->stop($request);
        }

        $request->session()->put(self::SESSION_KEY, $admin->id);
        Auth::login($target);
        $request->session()->regenerate();

        return redirect()
            ->route(PanelRegistry::homeRouteFor($target))
            ->with('status', 'نمای «'.$target->roleLabel().'» با حساب «'.$target->name.'» فعال شد.');
    }

    public function stop(Request $request): RedirectResponse
    {
        $adminId = $request->session()->pull(self::SESSION_KEY);
        $admin = $adminId ? User::query()->find($adminId) : null;

        if ($admin === null || ! $admin->isAdmin()) {
            return redirect()->route('home');
        }

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()
            ->route('panel.admin.users.index')
            ->with('status', 'به حساب مدیر بازگشتید.');
    }
}
