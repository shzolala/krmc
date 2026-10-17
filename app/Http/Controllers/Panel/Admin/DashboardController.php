<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Enums\ProgramStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Attraction;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\Program;
use App\Models\Shelter;
use App\Models\Shop;
use App\Models\User;
use App\Support\Admin\AdminResources;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Review\Enums\ReviewStatus;
use Modules\Review\Models\Review;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $pendingReviews = Review::query()->where('status', ReviewStatus::Pending)->count();

        $roleCounts = User::query()
            ->select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $inventory = collect(AdminResources::all())->map(fn ($resource): array => [
            'resource' => $resource,
            'count' => $resource->model()::query()->count(),
        ])->values();

        return view('panel.admin.dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'new_users_week' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
                'clubs' => Club::query()->count(),
                'coaches' => CoachProfile::query()->count(),
                'shops' => Shop::query()->count(),
                'destinations' => Destination::query()->count(),
                'active_programs' => Program::query()->active()->count(),
                'pending_reviews' => $pendingReviews,
                'total_views' => (int) Destination::query()->sum('view_count')
                    + (int) Club::query()->sum('view_count')
                    + (int) Attraction::query()->sum('view_count'),
            ],
            'attention' => $this->attentionItems($pendingReviews),
            'roleCounts' => collect(UserRole::cases())->map(fn (UserRole $role): array => [
                'role' => $role,
                'count' => (int) ($roleCounts[$role->value] ?? 0),
            ]),
            'signups' => $this->signupsByDay(14),
            'inventory' => $inventory,
            'recentUsers' => User::query()->latest()->limit(6)->get(),
            'pendingReviewList' => Review::query()
                ->with(['user:id,name', 'reviewable'])
                ->where('status', ReviewStatus::Pending)
                ->latest()
                ->limit(4)
                ->get(),
            'topDestinations' => Destination::query()->orderByDesc('view_count')->limit(5)->get(['id', 'name', 'slug', 'view_count', 'category']),
            'upcomingPrograms' => Program::query()
                ->with('club:id,name')
                ->whereIn('status', [ProgramStatus::Upcoming, ProgramStatus::Ongoing])
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
        ]);
    }

    /**
     * @return list<array{label: string, count: int, url: string, tone: string}>
     */
    private function attentionItems(int $pendingReviews): array
    {
        $items = [
            ['label' => 'نظر در انتظار تأیید', 'count' => $pendingReviews, 'url' => route('panel.admin.reviews'), 'tone' => 'amber'],
            ['label' => 'باشگاه تأییدنشده', 'count' => Club::query()->where('is_verified', false)->count(), 'url' => route('panel.admin.resources.index', ['resource' => 'clubs', 'is_verified' => 0]), 'tone' => 'blue'],
            ['label' => 'مربی / سرپرست تأییدنشده', 'count' => CoachProfile::query()->where('is_verified', false)->count(), 'url' => route('panel.admin.resources.index', ['resource' => 'coaches', 'is_verified' => 0]), 'tone' => 'green'],
            ['label' => 'فروشگاه تأییدنشده', 'count' => Shop::query()->where('is_verified', false)->count(), 'url' => route('panel.admin.resources.index', ['resource' => 'shops', 'is_verified' => 0]), 'tone' => 'purple'],
            ['label' => 'اقامتگاه تأییدنشده', 'count' => Accommodation::query()->where('is_verified', false)->count(), 'url' => route('panel.admin.resources.index', ['resource' => 'accommodations', 'is_verified' => 0]), 'tone' => 'teal'],
            ['label' => 'پناهگاه بدون مختصات', 'count' => Shelter::query()->whereNull('latitude')->orWhereNull('longitude')->count(), 'url' => route('panel.admin.resources.index', ['resource' => 'shelters']), 'tone' => 'red'],
        ];

        return array_values(array_filter($items, fn (array $item): bool => $item['count'] > 0));
    }

    /**
     * @return list<array{date: Carbon, count: int}>
     */
    private function signupsByDay(int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $counts = User::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->countBy(fn (User $user): string => $user->created_at->toDateString());

        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $series[] = ['date' => $date, 'count' => (int) ($counts[$date->toDateString()] ?? 0)];
        }

        return $series;
    }
}
