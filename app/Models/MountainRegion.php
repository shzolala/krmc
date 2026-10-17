<?php

namespace App\Models;

use App\Enums\DestinationCategory;
use App\Enums\MountainRegionType;
use App\Models\Concerns\HasGeoLocation;
use Database\Factories\MountainRegionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'catalog_id',
    'name',
    'name_en',
    'slug',
    'alt_names',
    'type',
    'province',
    'counties',
    'nearest_city',
    'latitude',
    'longitude',
    'bbox',
    'highest_elevation',
    'highest_peak_catalog_id',
    'summary',
    'description',
    'cover_path',
    'seo',
    'notes',
    'data_confidence',
    'is_featured',
    'view_count',
])]
class MountainRegion extends Model
{
    /** @use HasFactory<MountainRegionFactory> */
    use HasFactory;

    use HasGeoLocation;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ...$this->geoLocationCasts(),
            'alt_names' => 'array',
            'counties' => 'array',
            'bbox' => 'array',
            'seo' => 'array',
            'notes' => 'array',
            'data_confidence' => 'array',
            'type' => MountainRegionType::class,
            'is_featured' => 'boolean',
        ];
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }

    public function peaks(): HasMany
    {
        return $this->destinations()->where('category', DestinationCategory::Peak);
    }

    public function natureSpots(): HasMany
    {
        return $this->destinations()->where('category', DestinationCategory::NatureSpot);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function typeLabel(): string
    {
        return $this->type instanceof MountainRegionType
            ? $this->type->label()
            : (string) $this->type;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function containsCoordinates(?float $latitude, ?float $longitude): bool
    {
        if ($latitude === null || $longitude === null || ! is_array($this->bbox)) {
            return false;
        }

        $minLat = $this->bbox['min_lat'] ?? null;
        $maxLat = $this->bbox['max_lat'] ?? null;
        $minLng = $this->bbox['min_lng'] ?? null;
        $maxLng = $this->bbox['max_lng'] ?? null;

        if ($minLat === null || $maxLat === null || $minLng === null || $maxLng === null) {
            return false;
        }

        return $latitude >= (float) $minLat
            && $latitude <= (float) $maxLat
            && $longitude >= (float) $minLng
            && $longitude <= (float) $maxLng;
    }
}
