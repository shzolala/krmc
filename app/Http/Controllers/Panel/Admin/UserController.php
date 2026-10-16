<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\MobileNumber;
use App\Support\Username;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('primaryClub:id,name')
            ->latest()
            ->paginate(20);

        return view('panel.admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('panel.admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'mobile' => MobileNumber::normalize($request->input('mobile')),
            'username' => Username::normalize($request->input('username')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'mobile' => ['required', 'string', MobileNumber::rule(), Rule::unique('users', 'mobile')->ignore($user->id)],
            'username' => ['required', 'string', 'min:3', 'max:30', Username::rule(), Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user->update($validated);

        return redirect()
            ->route('panel.admin.users.index')
            ->with('status', 'اطلاعات کاربر به‌روزرسانی شد.');
    }
}
