<?php

namespace App\Support;

use App\Models\Accommodation;
use App\Models\Attraction;
use App\Models\Club;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MapCatalog
{
    private const CACHE_KEY = 'map.catalog';

    private const CACHE_TTL_SECONDS = 600;

    /**
     * @return list<class-string<Model>>
     */
    public static function sourceModels(): array
    {
        return [
            MountainRegion::class,
            Destination::class,
            Attraction::class,
            Club::class,
            Accommodation::class,
            Shelter::class,
            Shop::class,
            ProgramReport::class,
        ];
    }

    /**
     * Clears the cached catalog whenever a source record is saved or deleted.
     */
    public static function flushWhenChanged(): void
    {
        foreach (self::sourceModels() as $model) {
            $model::saved(fn () => self::flush());
            $model::deleted(fn () => self::flush());
        }
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Same as build(), served from the cache for a few minutes.
     *
     * @return array{markers: Collection<int, array<string, mixed>>, counts: array<string, int>, layers: array<string, array{label: string, singular: string, color: string}>}
     */
    public function cached(): array
    {
        /** Only plain arrays are cached: the cache store does not unserialize objects such as Collection. */
        $cached = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $catalog = $this->build();
            $catalog['markers'] = $catalog['markers']->all();

            return $catalog;
        });

        $cached['markers'] = collect($cached['markers']);

        return $cached;
    }

    /**
     * @return array{markers: Collection<int, array<string, mixed>>, counts: array<string, int>, layers: array<string, array{label: string, singular: string, color: string}>}
     */
    public function build(): array
    {
        $layers = $this->layers();

        $markers = collect()
            ->merge($this->regionMarkers())
            ->merge($this->destinationMarkers())
            ->merge($this->attractionMarkers())
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
     * `label` names the layer as a filter chip, `singular` names a single place of that layer on cards and popups.
     *
     * @return array<string, array{label: string, singular: string, color: string}>
     */
    public function layers(): array
    {
        return [
            'region' => ['label' => 'کوهستان‌ها', 'singular' => 'منطقه کوهستانی', 'color' => '#0f766e'],
            'destination' => ['label' => 'قله‌ها و طبیعت', 'singular' => 'قله / مقصد', 'color' => '#d4a017'],
            'attraction' => ['label' => 'دیدنی‌ها', 'singular' => 'جاذبه گردشگری', 'color' => '#7c3aed'],
            'accommodation' => ['label' => 'اقامت', 'singular' => 'اقامتگاه', 'color' => '#2563eb'],
            'shelter' => ['label' => 'پناهگاه‌ها', 'singular' => 'پناهگاه', 'color' => '#b45309'],
            'program' => ['label' => 'برنامه‌های اخیر', 'singular' => 'گزارش برنامه', 'color' => '#c026d3'],
            'club' => ['label' => 'باشگاه‌ها', 'singular' => 'باشگاه', 'color' => '#0b1f3a'],
            'shop' => ['label' => 'تجهیزات', 'singular' => 'فروشگاه', 'color' => '#1a7a4c'],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function regionMarkers(): Collection
    {
        return MountainRegion::query()
            ->withCoordinates()
            ->withCount(['destinations', 'attractions'])
            ->orderByDesc('is_featured')
            ->orderByDesc('highest_elevation')
            ->get()
            ->map(function (MountainRegion $item): ?array {
                $badges = [];
                if ($item->is_featured) {
                    $badges[] = 'منطقه پیشنهادی';
                }

                return $item->toMapMarker('region', route('regions.show', $item), [
                    'subtitle' => $item->typeLabel(),
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->highest_elevation ? fa_num($item->highest_elevation).' متر' : null,
                        $item->destinations_count ? fa_num($item->destinations_count).' مقصد' : null,
                        $item->attractions_count ? fa_num($item->attractions_count).' جاذبه' : null,
                    ])->filter()->implode(' · '),
                    'featured' => (bool) $item->is_featured,
                    'sort' => (int) ($item->highest_elevation ?? $item->view_count),
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function destinationMarkers(): Collection
    {
        return Destination::query()
            ->with('mountainRegion')
            ->withCoordinates()
            ->withCount(['programReports', 'accommodations', 'shelters'])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->get()
            ->map(function (Destination $item): ?array {
                $badges = [];
                if ($item->is_featured) {
                    $badges[] = 'مقصد پیشنهادی';
                }
                if ($item->program_reports_count > 0) {
                    $badges[] = fa_num($item->program_reports_count).' برنامه';
                }

                return $item->toMapMarker('destination', route('destinations.show', $item), [
                    'subtitle' => $item->mountainRegion?->name ?: ($item->region ?: $item->categoryLabel()),
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->categoryLabel(),
                        $item->elevation ? fa_num($item->elevation).' متر' : null,
                        $item->accommodations_count ? fa_num($item->accommodations_count).' اقامتگاه' : null,
                        $item->shelters_count ? fa_num($item->shelters_count).' پناهگاه' : null,
                    ])->filter()->implode(' · '),
                    'featured' => (bool) $item->is_featured,
                    'sort' => (int) $item->view_count,
                ]);
            });
    }

    /**
     * @return Collection<int, array<string, mixed>|null>
     */
    protected function attractionMarkers(): Collection
    {
        return Attraction::query()
            ->with('mountainRegion')
            ->withCoordinates()
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->get()
            ->map(function (Attraction $item): ?array {
                $badges = [];
                if ($item->is_featured) {
                    $badges[] = 'پیشنهادی';
                }
                if ($item->isUnesco()) {
                    $badges[] = 'یونسکو';
                }

                return $item->toMapMarker('attraction', route('attractions.show', $item), [
                    'subtitle' => $item->mountainRegion?->name ?: ($item->city ?: $item->typeLabel()),
                    'summary' => $item->summary,
                    'cover' => $item->cover_path,
                    'badges' => $badges,
                    'meta' => collect([
                        $item->typeLabel(),
                        $item->city,
                        $item->era,
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
                        $item->member_count ? fa_num($item->member_count).' عضو' : null,
                        $item->program_count ? fa_num($item->program_count).' برنامه' : null,
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
                        $item->elevation ? fa_num($item->elevation).' متر' : null,
                        $item->capacity ? 'ظرفیت '.fa_num($item->capacity).' نفر' : null,
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
