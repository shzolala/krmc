<?php

namespace App\Models;

use App\Enums\AttractionType;
use App\Models\Concerns\HasGeoLocation;
use Database\Factories\AttractionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'catalog_id',
    'mountain_region_id',
    'name',
    'name_en',
    'slug',
    'alt_names',
    'type',
    'types',
    'era',
    'unesco',
    'province',
    'county',
    'city',
    'latitude',
    'longitude',
    'coordinate_meta',
    'summary',
    'description',
    'cover_path',
    'visiting',
    'extras',
    'seo',
    'sources',
    'data_confidence',
    'is_featured',
    'view_count',
])]
class Attraction extends Model
{
    /** @use HasFactory<AttractionFactory> */
    use HasFactory;

    use HasGeoLocation;
    use HasReviews;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ...$this->geoLocationCasts(),
            'alt_names' => 'array',
            'types' => 'array',
            'unesco' => 'array',
            'coordinate_meta' => 'array',
            'visiting' => 'array',
            'extras' => 'array',
            'seo' => 'array',
            'sources' => 'array',
            'data_confidence' => 'array',
            'type' => AttractionType::class,
            'is_featured' => 'boolean',
        ];
    }

    public function mountainRegion(): BelongsTo
    {
        return $this->belongsTo(MountainRegion::class);
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)
            ->withPivot('relation')
            ->withTimestamps();
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
        return $this->type instanceof AttractionType
            ? $this->type->label()
            : (string) $this->type;
    }

    public function isUnesco(): bool
    {
        return (bool) data_get($this->unesco, 'inscribed');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
