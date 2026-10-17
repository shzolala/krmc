<?php

namespace App\Models;

use App\Enums\DestinationCategory;
use App\Models\Concerns\HasGeoLocation;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'catalog_id',
    'mountain_region_id',
    'name',
    'name_en',
    'slug',
    'category',
    'subtype',
    'alt_names',
    'region',
    'province',
    'county',
    'nearest_city',
    'nearest_village',
    'elevation',
    'summary',
    'description',
    'description_is_short',
    'cover_path',
    'latitude',
    'longitude',
    'coordinate_meta',
    'address',
    'trail_info',
    'extras',
    'seo',
    'sources',
    'data_confidence',
    'is_featured',
    'view_count',
])]
class Destination extends Model
{
    /** @use HasFactory<DestinationFactory> */
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
            'coordinate_meta' => 'array',
            'trail_info' => 'array',
            'extras' => 'array',
            'seo' => 'array',
            'sources' => 'array',
            'data_confidence' => 'array',
            'category' => DestinationCategory::class,
            'description_is_short' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function mountainRegion(): BelongsTo
    {
        return $this->belongsTo(MountainRegion::class);
    }

    public function guides(): HasMany
    {
        return $this->hasMany(DestinationGuide::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function programReports(): HasMany
    {
        return $this->hasMany(ProgramReport::class);
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function shelters(): HasMany
    {
        return $this->hasMany(Shelter::class);
    }

    public function attractions(): BelongsToMany
    {
        return $this->belongsToMany(Attraction::class)
            ->withPivot('relation')
            ->withTimestamps();
    }

    public function relatedDestinations(): BelongsToMany
    {
        return $this->belongsToMany(
            Destination::class,
            'destination_related',
            'destination_id',
            'related_destination_id',
        )->withPivot('relation')->withTimestamps();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function peaks(Builder $query): Builder
    {
        return $query->where('category', DestinationCategory::Peak);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function natureSpots(Builder $query): Builder
    {
        return $query->where('category', DestinationCategory::NatureSpot);
    }

    public function categoryLabel(): string
    {
        return $this->category instanceof DestinationCategory
            ? $this->category->label()
            : 'مقصد';
    }

    public function difficultyGrade(): ?int
    {
        $grade = data_get($this->trail_info, 'difficulty.grade_1_5');

        return is_numeric($grade) ? (int) $grade : null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
