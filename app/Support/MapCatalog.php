<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Club;
use App\Models\Destination;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Shop;
use Illuminate\Support\Collection;

class MapCatalog
{
    /**
     * @return array{markers: Collection<int, array<string, mixed>>, counts: array<string, int>, layers: array<string, array{label: string, color: string}>}
     */
    public function build(): array
    {
        $layers = $this->layers();

        $markers = collect()
            ->merge($this->destinationMarkers())
            ->merge($this->clubMarkers())
            ->merge($this->accommodationMarkers())
            ->merge($this->shelterMarkers())
            ->merge($this->shopMarkers())
            ->merge($this->programMarkers())
            ->filter()
            ->values();

        $counts = $markers
            ->groupBy('type')
            ->map(fn (Collection $group): int => $group->count())
            ->all();

        foreach (array_keys($layers) as $type) {
            $counts[$type] = $counts[$type] ?? 0;
        }

        return [
            'markers' => $markers,
            'counts' => $counts,
            'layers' => $layers,
        ];
    }

    /**
     * @return array<string, array{label: string, color: string}>
     */
    public function layers(): array
    {
        return [
            'destination' => ['label' => 'مناطق و مقاصد', 'color' => '#d4a017'],
            'accommodation' => ['label' => 'اقامتگاه‌ها', 'color' => '#2563eb'],
            'shelter' => ['label' => 'پناهگاه‌ها', 'color' => '#b45309'],
            'program' => ['label' => 'برنامه‌ها', 'color' => '#c026d3'],
            'club' => ['label' => 'باشگاه‌ها', 'color' => '#0b1f3a'],
            'shop' => ['label' => 'فروشگاه‌ها', 'color' => '#1a7a4c'],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function destinationMarkers(): Collection
    {
        return Destination::query()
            ->withCoordinates()
            ->withCount(['programReports', 'accommodations', 'shelters'])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->get()
            ->map(function (Destination $item): ?array {
                $badges = [];
                if ($item->is_featured) {
                    $badges[] = 'منطقه پیشنهادی';
                }
                if ($item->program_reports_count > 0) {
                    $badges[] = $item->program_reports_count.' برنامه';
                }

                return $item->toMapMarker('destination', route('destinations.show', $item), [
                    'subtitle' => $item->region ?: 'مقصد کوهنوردی',
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->elevation ? number_format($item->elevation).' متر' : null,
                        $item->accommodations_count ? $item->accommodations_count.' اقامتگاه' : null,
                        $item->shelters_count ? $item->shelters_count.' پناهگاه' : null,
                    ])->filter()->implode(' · '),
                    'featured' => (bool) $item->is_featured,
                    'sort' => (int) $item->view_count,
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function clubMarkers(): Collection
    {
        return Club::query()
            ->withCoordinates()
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderByDesc('member_count')
            ->get()
            ->map(function (Club $item): ?array {
                $badges = [];
                if ($item->is_premium) {
                    $badges[] = 'پرمیوم';
                }
                if ($item->is_verified) {
                    $badges[] = 'تأییدشده';
                }

                return $item->toMapMarker('club', route('clubs.show', $item), [
                    'subtitle' => $item->city ?: 'باشگاه',
                    'summary' => $item->summary,
                    'cover' => $item->cover_path ?: $item->logo_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->member_count ? number_format($item->member_count).' عضو' : null,
                        $item->program_count ? number_format($item->program_count).' برنامه' : null,
                    ])->filter()->implode(' · '),
                    'featured' => (bool) ($item->is_featured || $item->is_premium),
                    'sort' => (int) $item->member_count,
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function accommodationMarkers(): Collection
    {
        return Accommodation::query()
            ->withCoordinates()
            ->with('destination:id,name,slug')
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->get()
            ->map(function (Accommodation $item): ?array {
                $badges = [];
                if ($item->is_premium) {
                    $badges[] = 'پرمیوم';
                }
                if ($item->is_verified) {
                    $badges[] = 'تأییدشده';
                }
                if ($item->type) {
                    $badges[] = $item->type->label();
                }

                return $item->toMapMarker('accommodation', route('accommodations.show', $item), [
                    'subtitle' => collect([
                        $item->city,
                        $item->destination?->name ? 'نزدیک '.$item->destination->name : null,
                    ])->filter()->implode(' · ') ?: 'اقامتگاه',
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => $item->address,
                    'featured' => (bool) ($item->is_featured || $item->is_premium),
                    'sort' => (int) $item->view_count,
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function shelterMarkers(): Collection
    {
        return Shelter::query()
            ->withCoordinates()
            ->with('destination:id,name,slug')
            ->orderByDesc('is_featured')
            ->get()
            ->map(function (Shelter $item): ?array {
                $badges = ['پناهگاه'];
                if ($item->is_featured) {
                    $badges[] = 'ویژه';
                }

                return $item->toMapMarker('shelter', route('shelters.show', $item), [
                    'subtitle' => $item->destination?->name
                        ? 'مسیر '.$item->destination->name
                        : 'پناهگاه کوهستانی',
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->elevation ? number_format($item->elevation).' متر' : null,
                        $item->capacity ? 'ظرفیت '.$item->capacity.' نفر' : null,
                    ])->filter()->implode(' · '),
                    'featured' => (bool) $item->is_featured,
                    'sort' => (int) $item->view_count,
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function shopMarkers(): Collection
    {
        return Shop::query()
            ->withCoordinates()
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->get()
            ->map(function (Shop $item): ?array {
                $badges = [];
                if ($item->is_premium) {
                    $badges[] = 'پرمیوم';
                }
                if ($item->is_verified) {
                    $badges[] = 'تأییدشده';
                }

                return $item->toMapMarker('shop', route('shops.show', $item), [
                    'subtitle' => $item->city ?: 'فروشگاه تجهیزات',
                    'summary' => $item->summary,
                    'cover' => $item->logo_path,
                    'badges' => $badges,
                    'meta' => $item->address,
                    'featured' => (bool) ($item->is_featured || $item->is_premium),
                    'sort' => $item->is_premium ? 2 : ($item->is_featured ? 1 : 0),
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function programMarkers(): Collection
    {
        return ProgramReport::query()
            ->with(['destination', 'club:id,name,slug'])
            ->whereHas('destination', fn ($query) => $query->withCoordinates())
            ->latest('published_at')
            ->limit(24)
            ->get()
            ->values()
            ->map(function (ProgramReport $item, int $index): ?array {
                $destination = $item->destination;
                if ($destination === null || ! $destination->hasCoordinates()) {
                    return null;
                }

                $badges = ['گزارش برنامه'];
                if ($item->published_at !== null && $item->published_at->isAfter(now()->subDays(30))) {
                    $badges[] = 'اخیر';
                }

                // Slight offset so stacked programs near the same peak remain clickable.
                $latNudge = (($index % 5) - 2) * 0.012;
                $lngNudge = (((int) floor($index / 5) % 5) - 2) * 0.012;

                return [
                    'id' => 'program-'.$item->getKey(),
                    'lat' => (float) $destination->latitude + $latNudge,
                    'lng' => (float) $destination->longitude + $lngNudge,
                    'label' => (string) $item->title,
                    'type' => 'program',
                    'url' => route('destinations.show', $destination),
                    'subtitle' => collect([
                        $destination->name,
                        $item->club?->name,
                    ])->filter()->implode(' · '),
                    'summary' => $item->excerpt,
                    'cover' => $item->cover_path ?: $destination->cover_path,
                    'badges' => $badges,
                    'meta' => $item->published_at?->diffForHumans(),
                    'featured' => $item->published_at !== null && $item->published_at->isAfter(now()->subDays(45)),
                    'sort' => $item->published_at?->timestamp ?? 0,
                ];
            });
    }
}
