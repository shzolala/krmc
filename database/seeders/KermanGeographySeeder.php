<?php

namespace Database\Seeders;

use App\Enums\DestinationCategory;
use App\Models\Attraction;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Support\CatalogCover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class KermanGeographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionsPayload = $this->loadJson('regions.json');
        $peaksPayload = $this->loadJson('peaks.json');
        $attractionsPayload = $this->loadJson('attractions.json');

        /** @var array<int, array<string, mixed>> $regionRows */
        $regionRows = $regionsPayload['regions'] ?? [];
        /** @var array<int, array<string, mixed>> $peakRows */
        $peakRows = $peaksPayload['items'] ?? [];
        /** @var array<int, array<string, mixed>> $attractionRows */
        $attractionRows = $attractionsPayload['attractions'] ?? [];

        $regionsByCatalogId = collect();

        foreach ($regionRows as $index => $row) {
            $region = $this->seedRegion($row, $index);
            $regionsByCatalogId->put($region->catalog_id, $region);
        }

        $destinationsByCatalogId = collect();

        foreach ($peakRows as $index => $row) {
            $destination = $this->seedDestination($row, $regionsByCatalogId, $index);
            $destinationsByCatalogId->put($destination->catalog_id, $destination);
        }

        $attractionsByCatalogId = collect();

        foreach ($attractionRows as $index => $row) {
            $attraction = $this->seedAttraction($row, $regionsByCatalogId, $destinationsByCatalogId, $index);
            $attractionsByCatalogId->put($attraction->catalog_id, $attraction);
        }

        $this->syncNearbyRelations($peakRows, $destinationsByCatalogId, $attractionsByCatalogId);
        $this->assignAttractionRegionsFromNearbyPeaks($attractionsByCatalogId);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function seedRegion(array $row, int $index): MountainRegion
    {
        $catalogId = (string) $row['id'];
        $seo = is_array($row['seo'] ?? null) ? $row['seo'] : [];
        $location = is_array($row['location'] ?? null) ? $row['location'] : [];
        $center = is_array($row['center'] ?? null) ? $row['center'] : [];
        $highest = is_array($row['highest_point'] ?? null) ? $row['highest_point'] : [];
        $description = (string) ($row['description_fa'] ?? '');

        return MountainRegion::query()->updateOrCreate(
            ['catalog_id' => $catalogId],
            [
                'name' => (string) $row['name_fa'],
                'name_en' => $this->nullableString($row['name_en'] ?? null),
                'slug' => (string) ($seo['slug'] ?? Str::slug($catalogId)),
                'alt_names' => $row['alt_names'] ?? [],
                'type' => (string) $row['type'],
                'province' => $this->nullableString($location['province'] ?? null),
                'counties' => $location['counties'] ?? [],
                'nearest_city' => $this->nullableString($location['nearest_city'] ?? null),
                'latitude' => $this->nullableFloat($center['lat'] ?? null),
                'longitude' => $this->nullableFloat($center['lng'] ?? null),
                'bbox' => $row['bbox'] ?? null,
                'highest_elevation' => $this->nullableInt($highest['elevation_m'] ?? null),
                'highest_peak_catalog_id' => $this->nullableString($highest['peak_id'] ?? null),
                'summary' => $this->summaryFrom($description, $seo['meta_description'] ?? null),
                'description' => $description !== '' ? $description : null,
                'cover_path' => CatalogCover::for('region-'.$catalogId),
                'seo' => $seo,
                'notes' => $row['notes'] ?? null,
                'data_confidence' => $row['data_confidence'] ?? null,
                'is_featured' => $index < 6 || ((int) ($highest['elevation_m'] ?? 0)) >= 4000,
                'view_count' => max(80, ((int) ($highest['elevation_m'] ?? 1000)) / 2),
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<string, MountainRegion>  $regionsByCatalogId
     */
    private function seedDestination(array $row, Collection $regionsByCatalogId, int $index): Destination
    {
        $catalogId = (string) $row['id'];
        $regionCatalogId = $this->nullableString($row['region_id'] ?? null);
        $region = $regionCatalogId ? $regionsByCatalogId->get($regionCatalogId) : null;
        $seo = is_array($row['seo'] ?? null) ? $row['seo'] : [];
        $location = is_array($row['location'] ?? null) ? $row['location'] : [];
        $coordinates = is_array($row['coordinates'] ?? null) ? $row['coordinates'] : [];
        $main = is_array($row['main'] ?? null) ? $row['main'] : [];
        $description = (string) ($row['description_fa'] ?? '');
        $category = (string) ($row['category'] ?? DestinationCategory::Peak->value);
        $elevation = $this->nullableInt($row['elevation_m'] ?? null);
        $nearestCity = $this->nullableString($location['nearest_city'] ?? null);

        return Destination::query()->updateOrCreate(
            ['catalog_id' => $catalogId],
            [
                'mountain_region_id' => $region?->id,
                'name' => (string) $row['name_fa'],
                'name_en' => $this->nullableString($row['name_en'] ?? null),
                'slug' => (string) ($seo['slug'] ?? Str::slug($catalogId)),
                'category' => $category,
                'subtype' => $this->nullableString($row['subtype'] ?? null),
                'alt_names' => $row['alt_names'] ?? [],
                'region' => $region?->name ?? ($location['province'] ?? 'کرمان'),
                'province' => $this->nullableString($location['province'] ?? null),
                'county' => $this->nullableString($location['county'] ?? null),
                'nearest_city' => $nearestCity,
                'nearest_village' => $this->nullableString($location['nearest_village'] ?? null),
                'elevation' => $elevation,
                'summary' => $this->summaryFrom($description, $seo['meta_description'] ?? null),
                'description' => $description !== '' ? $description : null,
                'description_is_short' => (bool) ($row['description_is_short'] ?? false),
                'cover_path' => CatalogCover::for('destination-'.$catalogId),
                'latitude' => $this->nullableFloat($coordinates['lat'] ?? null),
                'longitude' => $this->nullableFloat($coordinates['lng'] ?? null),
                'coordinate_meta' => $this->coordinateMeta($coordinates),
                'address' => $this->destinationAddress($location, $nearestCity),
                'trail_info' => $main !== [] ? $main : null,
                'extras' => $row['extras'] ?? null,
                'seo' => $seo,
                'sources' => $row['sources'] ?? null,
                'data_confidence' => $row['data_confidence'] ?? null,
                'is_featured' => $category === DestinationCategory::Peak->value
                    && ($index < 12 || ($elevation ?? 0) >= 4000),
                'view_count' => max(50, ($elevation ?? 1500) + ($index * 7)),
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<string, MountainRegion>  $regionsByCatalogId
     * @param  Collection<string, Destination>  $destinationsByCatalogId
     */
    private function seedAttraction(
        array $row,
        Collection $regionsByCatalogId,
        Collection $destinationsByCatalogId,
        int $index,
    ): Attraction {
        $catalogId = (string) $row['id'];
        $seo = is_array($row['seo'] ?? null) ? $row['seo'] : [];
        $location = is_array($row['location'] ?? null) ? $row['location'] : [];
        $coordinates = is_array($row['coordinates'] ?? null) ? $row['coordinates'] : [];
        $description = (string) ($row['description_fa'] ?? '');
        $latitude = $this->nullableFloat($coordinates['lat'] ?? null);
        $longitude = $this->nullableFloat($coordinates['lng'] ?? null);
        $unesco = is_array($row['unesco'] ?? null) ? $row['unesco'] : null;

        $region = $this->resolveAttractionRegion($latitude, $longitude, $regionsByCatalogId);

        return Attraction::query()->updateOrCreate(
            ['catalog_id' => $catalogId],
            [
                'mountain_region_id' => $region?->id,
                'name' => (string) $row['name_fa'],
                'name_en' => $this->nullableString($row['name_en'] ?? null),
                'slug' => (string) ($seo['slug'] ?? Str::slug($catalogId)),
                'alt_names' => $row['alt_names'] ?? [],
                'type' => (string) $row['type'],
                'types' => $row['types'] ?? [],
                'era' => $this->nullableString($row['era'] ?? null),
                'unesco' => $unesco,
                'province' => $this->nullableString($location['province'] ?? null),
                'county' => $this->nullableString($location['county'] ?? null),
                'city' => $this->nullableString($location['city'] ?? null),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'coordinate_meta' => $this->coordinateMeta($coordinates),
                'summary' => $this->summaryFrom($description, $seo['meta_description'] ?? null),
                'description' => $description !== '' ? $description : null,
                'cover_path' => CatalogCover::for('attraction-'.$catalogId),
                'visiting' => $row['visiting'] ?? null,
                'extras' => $row['extras'] ?? null,
                'seo' => $seo,
                'sources' => $row['sources'] ?? null,
                'data_confidence' => $row['data_confidence'] ?? null,
                'is_featured' => $index < 8 || (bool) data_get($unesco, 'inscribed'),
                'view_count' => max(40, 200 + ($index * 11)),
            ]
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $peakRows
     * @param  Collection<string, Destination>  $destinationsByCatalogId
     * @param  Collection<string, Attraction>  $attractionsByCatalogId
     */
    private function syncNearbyRelations(
        array $peakRows,
        Collection $destinationsByCatalogId,
        Collection $attractionsByCatalogId,
    ): void {
        foreach ($peakRows as $row) {
            $destination = $destinationsByCatalogId->get((string) $row['id']);

            if ($destination === null) {
                continue;
            }

            $nearby = data_get($row, 'extras.nearby_attractions', []);

            if (! is_array($nearby) || $nearby === []) {
                continue;
            }

            $attractionSync = [];
            $relatedSync = [];

            foreach ($nearby as $catalogId) {
                if (! is_string($catalogId) || $catalogId === '') {
                    continue;
                }

                if (str_starts_with($catalogId, 'att-')) {
                    $attraction = $attractionsByCatalogId->get($catalogId);

                    if ($attraction !== null) {
                        $attractionSync[$attraction->id] = ['relation' => 'nearby'];
                    }

                    continue;
                }

                $related = $destinationsByCatalogId->get($catalogId);

                if ($related !== null && $related->id !== $destination->id) {
                    $relatedSync[$related->id] = ['relation' => 'nearby'];
                }
            }

            $destination->attractions()->sync($attractionSync);
            $destination->relatedDestinations()->sync($relatedSync);
        }
    }

    /**
     * @param  Collection<string, Attraction>  $attractionsByCatalogId
     */
    private function assignAttractionRegionsFromNearbyPeaks(Collection $attractionsByCatalogId): void
    {
        foreach ($attractionsByCatalogId as $attraction) {
            if ($attraction->mountain_region_id !== null) {
                continue;
            }

            $nearbyDestination = $attraction->destinations()
                ->whereNotNull('mountain_region_id')
                ->orderByDesc('elevation')
                ->first();

            if ($nearbyDestination?->mountain_region_id) {
                $attraction->update([
                    'mountain_region_id' => $nearbyDestination->mountain_region_id,
                ]);
            }
        }
    }

    /**
     * @param  Collection<string, MountainRegion>  $regionsByCatalogId
     */
    private function resolveAttractionRegion(
        ?float $latitude,
        ?float $longitude,
        Collection $regionsByCatalogId,
    ): ?MountainRegion {
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return $regionsByCatalogId
            ->first(fn (MountainRegion $region): bool => $region->containsCoordinates($latitude, $longitude));
    }

    /**
     * @param  array<string, mixed>  $coordinates
     * @return array<string, mixed>|null
     */
    private function coordinateMeta(array $coordinates): ?array
    {
        $meta = array_filter([
            'datum' => $coordinates['datum'] ?? null,
            'source' => $coordinates['source'] ?? null,
            'accuracy' => $coordinates['accuracy'] ?? null,
        ], fn (mixed $value): bool => $value !== null && $value !== '');

        return $meta === [] ? null : $meta;
    }

    /**
     * @param  array<string, mixed>  $location
     */
    private function destinationAddress(array $location, ?string $nearestCity): ?string
    {
        $parts = array_filter([
            $nearestCity,
            $this->nullableString($location['nearest_village'] ?? null),
            $this->nullableString($location['county'] ?? null),
            $this->nullableString($location['province'] ?? null),
        ]);

        return $parts === [] ? null : implode(' · ', $parts);
    }

    private function summaryFrom(string $description, mixed $fallback): string
    {
        if (is_string($fallback) && trim($fallback) !== '') {
            return Str::limit(trim($fallback), 220);
        }

        $normalized = preg_replace('/\s+/u', ' ', trim($description)) ?? '';

        return Str::limit($normalized !== '' ? $normalized : 'اطلاعات این مکان به‌زودی تکمیل می‌شود.', 220);
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function loadJson(string $filename): array
    {
        $path = storage_path($filename);

        if (! File::exists($path)) {
            throw new RuntimeException("Missing seed data file: {$path}");
        }

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }
}
