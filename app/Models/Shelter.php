<?php

namespace App\Models;

use App\Models\Concerns\HasGeoLocation;
use Database\Factories\ShelterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'name',
    'slug',
    'summary',
    'description',
    'elevation',
    'capacity',
    'cover_path',
    'latitude',
    'longitude',
    'address',
    'destination_id',
    'is_featured',
    'view_count',
])]
class Shelter extends Model
{
    /** @use HasFactory<ShelterFactory> */
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

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
