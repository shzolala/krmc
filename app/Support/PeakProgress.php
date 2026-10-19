<?php

namespace App\Support;

use App\Models\ClimbBadge;
use App\Models\PeakAscent;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * A climber's progress on the peak ladder: ascents, earned badges and the next goal.
 */
class PeakProgress
{
    /**
     * @var Collection<int, PeakAscent>
     */
    private Collection $ascents;

    public function __construct(?User $user)
    {
        $this->ascents = $user === null
            ? collect()
            : $user->peakAscents()->with('destination:id,name,slug,elevation')->get()->filter(fn (PeakAscent $ascent): bool => $ascent->destination !== null);
    }

    public function count(): int
    {
        return $this->ascents->count();
    }

    /**
     * @return Collection<int, PeakAscent>
     */
    public function ascents(): Collection
    {
        return $this->ascents;
    }

    /**
     * Ascents keyed by destination id, for tick marks on the ladder.
     *
     * @return Collection<int, PeakAscent>
     */
    public function byDestination(): Collection
    {
        return $this->ascents->keyBy('destination_id');
    }

    public function countFor(ClimbBadge $badge): int
    {
        return $badge->min_elevation === null
            ? $this->ascents->count()
            : $this->ascents->filter(fn (PeakAscent $ascent): bool => (int) $ascent->destination->elevation >= $badge->min_elevation)->count();
    }

    /**
     * @return Collection<int, array{badge: ClimbBadge, current: int, required: int, earned: bool, percent: int}>
     */
    public function badges(): Collection
    {
        return ClimbBadge::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('required_count')
            ->get()
            ->map(function (ClimbBadge $badge): array {
                $current = $this->countFor($badge);

                return [
                    'badge' => $badge,
                    'current' => min($current, $badge->required_count),
                    'required' => $badge->required_count,
                    'earned' => $current >= $badge->required_count,
                    'percent' => (int) floor(min($current, $badge->required_count) / max(1, $badge->required_count) * 100),
                ];
            });
    }

    /**
     * The most demanding badge the climber has earned, shown as their title.
     *
     * @param  Collection<int, array{badge: ClimbBadge, current: int, required: int, earned: bool, percent: int}>  $badges
     * @return array{badge: ClimbBadge, current: int, required: int, earned: bool, percent: int}|null
     */
    public static function title(Collection $badges): ?array
    {
        return $badges->where('earned', true)->sortByDesc(fn (array $row): int => $row['required'] * ($row['badge']->min_elevation ? 2 : 1))->first();
    }

    /**
     * The closest unearned badge to complete.
     *
     * @param  Collection<int, array{badge: ClimbBadge, current: int, required: int, earned: bool, percent: int}>  $badges
     * @return array{badge: ClimbBadge, current: int, required: int, earned: bool, percent: int}|null
     */
    public static function next(Collection $badges): ?array
    {
        return $badges->where('earned', false)->sortByDesc('percent')->first();
    }
}
