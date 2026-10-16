<?php

namespace App\Models;

use App\Enums\LodgingType;
use App\Models\Concerns\HasGeoLocation;
use Database\Factories\AccommodationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'name',
    'slug',
    'type',
    'summary',
    'description',
    'city',
    'cover_path',
    'latitude',
    'longitude',
    'address',
    'destination_id',
    'is_verified',
    'is_featured',
    'is_premium',
    'view_count',
])]
class Accommodation extends Model
{
    /** @use HasFactory<AccommodationFactory> */
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
            'type' => LodgingType::class,
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
