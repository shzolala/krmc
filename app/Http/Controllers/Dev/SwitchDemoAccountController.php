<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MobileNumber;
use App\Support\PanelRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SwitchDemoAccountController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_unless(PanelRegistry::switcherEnabled(), 404);

        $validated = $request->validate([
            'mobile' => [
                'required',
                'string',
                Rule::in(PanelRegistry::demoAccounts()->pluck('mobile')->all()),
            ],
        ]);

        $mobile = MobileNumber::normalize($validated['mobile']);
        $user = User::query()->where('mobile', $mobile)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route(PanelRegistry::homeRouteFor($user))
            ->with('status', 'وارد حساب «'.$user->name.'» شدید.');
    }
}
