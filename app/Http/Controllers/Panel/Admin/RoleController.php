<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Admin\RoleCapabilities;
use App\Support\PanelRegistry;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __invoke(): View
    {
        $counts = User::countsByRole();

        $roles = collect(UserRole::cases())->map(fn (UserRole $role): array => [
            'role' => $role,
            'count' => (int) ($counts[$role->value] ?? 0),
            'panel' => PanelRegistry::forRole($role),
            'sample' => User::query()->withRole($role)->orderBy('id')->first(),
            ...$this->addAction($role),
        ]);

        return view('panel.admin.roles', [
            'roles' => $roles,
            'matrix' => RoleCapabilities::matrix(),
        ]);
    }

    /**
     * Business roles come from owning a club, shop or coach profile, so "add" opens that record's form.
     *
     * @return array{addUrl: string, addLabel: string}
     */
    private function addAction(UserRole $role): array
    {
        return match ($role) {
            UserRole::Admin => ['addUrl' => route('panel.admin.users.create', ['admin' => 1]), 'addLabel' => 'افزودن مدیر'],
            UserRole::Climber => ['addUrl' => route('panel.admin.users.create'), 'addLabel' => 'افزودن کاربر'],
            UserRole::Club => ['addUrl' => route('panel.admin.resources.create', 'clubs'), 'addLabel' => 'ثبت باشگاه'],
            UserRole::Shop => ['addUrl' => route('panel.admin.resources.create', 'shops'), 'addLabel' => 'ثبت فروشگاه'],
            UserRole::Coach, UserRole::Leader => ['addUrl' => route('panel.admin.resources.create', 'coaches'), 'addLabel' => 'ثبت پروفایل'],
        };
    }
}
