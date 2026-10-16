<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Shop;
use App\Models\User;
use Illuminate\View\View;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('panel.admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'clubs' => Club::query()->count(),
                'shops' => Shop::query()->count(),
                'pending_reviews' => Review::query()->where('status', ReviewStatus::Pending)->count(),
            ],
        ]);
    }
}
