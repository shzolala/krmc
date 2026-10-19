<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\PeakAscent;
use App\Models\User;
use App\Support\PeakProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PeakController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $progress = new PeakProgress($user);
        $climbed = $progress->byDestination();
        $badges = $progress->badges();

        $peaks = Destination::query()
            ->peaks()
            ->whereNotNull('elevation')
            ->withCount(['ascents as climbers_count'])
            ->orderByDesc('elevation')
            ->get();

        $filter = $user !== null && in_array($request->query('show'), ['todo', 'done'], true) ? $request->query('show') : null;

        $ranked = $peaks->values()->map(fn (Destination $peak, int $index): array => [
            'rank' => $index + 1,
            'peak' => $peak,
            'ascent' => $climbed->get($peak->id),
        ])->when($filter === 'todo', fn ($rows) => $rows->whereNull('ascent'))
            ->when($filter === 'done', fn ($rows) => $rows->whereNotNull('ascent'))
            ->values();

        return view('peaks.index', [
            'rows' => $ranked,
            'tallest' => (int) $peaks->max('elevation'),
            'totalPeaks' => $peaks->count(),
            'filter' => $filter,
            'progress' => $progress,
            'badges' => $badges,
            'title' => PeakProgress::title($badges),
            'next' => PeakProgress::next($badges),
            'leaders' => $user === null ? collect() : $this->leaders(),
        ]);
    }

    /**
     * @return Collection<int, array{user: User, count: int}>
     */
    private function leaders()
    {
        $counts = PeakAscent::query()
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'user_id');

        $users = User::query()->whereIn('id', $counts->keys())->get(['id', 'name', 'username'])->keyBy('id');

        return $counts->map(fn (int $total, int $userId): array => ['user' => $users[$userId], 'count' => $total])->values();
    }
}
