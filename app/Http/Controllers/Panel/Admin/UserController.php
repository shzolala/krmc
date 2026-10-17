<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Support\MobileNumber;
use App\Support\Username;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $role = UserRole::tryFrom((string) $request->query('role', ''));

        $users = User::query()
            ->with('primaryClub:id,name')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($where) use ($search): void {
                    $where->where('name', 'like', '%'.$search.'%')
                        ->orWhere('mobile', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->when($role !== null, fn ($query) => $query->where('role', $role))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $counts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        return view('panel.admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'counts' => $counts,
            'totalUsers' => $counts->sum(),
            'search' => $search,
            'activeRole' => $role,
        ]);
    }

    public function create(Request $request): View
    {
        return view('panel.admin.users.form', [
            'user' => new User(['role' => UserRole::tryFrom((string) $request->query('role', '')) ?? UserRole::Climber]),
            'roles' => UserRole::cases(),
            'clubs' => Club::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeInput($request);

        $validated = $request->validate([
            ...$this->profileRules(null),
            'password' => ['required', 'string', Password::default()],
        ]);

        $user = User::query()->create($validated);

        return redirect()
            ->route('panel.admin.users.edit', $user)
            ->with('status', 'کاربر «'.$user->name.'» ایجاد شد.');
    }

    public function edit(User $user): View
    {
        $user->load(['primaryClub', 'coachProfile.club', 'shop', 'ownedClubs']);

        return view('panel.admin.users.form', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'clubs' => Club::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->normalizeInput($request);

        $validated = $request->validate([
            ...$this->profileRules($user),
            'password' => ['nullable', 'string', Password::default()],
        ]);

        if ($user->is($request->user()) && $validated['role'] !== $user->role->value) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'نقش حساب خودتان را نمی‌توانید تغییر دهید.']);
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('panel.admin.users.index')
            ->with('status', 'اطلاعات کاربر به‌روزرسانی شد.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'حذف حساب خودتان ممکن نیست.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('panel.admin.users.index')
            ->with('status', 'کاربر «'.$name.'» حذف شد.');
    }

    private function normalizeInput(Request $request): void
    {
        $request->merge([
            'mobile' => MobileNumber::normalize($request->input('mobile')),
            'username' => Username::normalize($request->input('username')),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function profileRules(?User $user): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'mobile' => ['required', 'string', MobileNumber::rule(), Rule::unique('users', 'mobile')->ignore($user?->id)],
            'username' => ['required', 'string', 'min:3', 'max:30', Username::rule(), Rule::unique('users', 'username')->ignore($user?->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'primary_club_id' => ['nullable', 'integer', Rule::exists('clubs', 'id')],
        ];
    }
}
