<?php

namespace App\Models;

use App\Models\Concerns\HasGeoLocation;
use Database\Factories\ShopFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'user_id',
    'name',
    'slug',
    'summary',
    'city',
    'latitude',
    'longitude',
    'address',
    'logo_path',
    'is_verified',
    'is_featured',
    'is_premium',
])]
class Shop extends Model
{
    /** @use HasFactory<ShopFactory> */
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
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_premium' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
