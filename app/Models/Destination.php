<?php

namespace App\Models;

use App\Models\Concerns\HasGeoLocation;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'name',
    'slug',
    'region',
    'elevation',
    'summary',
    'description',
    'cover_path',
    'latitude',
    'longitude',
    'address',
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
            'is_featured' => 'boolean',
        ];
    }

    public function guides(): HasMany
    {
        return $this->hasMany(DestinationGuide::class);
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
