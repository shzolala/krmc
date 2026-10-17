<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Admin\RoleCapabilities;
use App\Support\PanelRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __invoke(): View
    {
        $counts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $roles = collect(UserRole::cases())->map(fn (UserRole $role): array => [
            'role' => $role,
            'count' => (int) ($counts[$role->value] ?? 0),
            'panel' => PanelRegistry::forRole($role),
            'sample' => User::query()->where('role', $role)->orderBy('id')->first(),
        ]);

        return view('panel.admin.roles', [
            'roles' => $roles,
            'matrix' => RoleCapabilities::matrix(),
        ]);
    }
}
