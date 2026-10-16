<?php

namespace App\Models;

use App\Models\Concerns\HasGeoLocation;
use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'name',
    'slug',
    'summary',
    'description',
    'city',
    'latitude',
    'longitude',
    'address',
    'founded_year',
    'logo_path',
    'cover_path',
    'is_verified',
    'is_featured',
    'is_premium',
    'member_count',
    'program_count',
    'view_count',
    'owner_id',
])]
class Club extends Model
{
    /** @use HasFactory<ClubFactory> */
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('status')->withTimestamps();
    }

    public function coachProfiles(): HasMany
    {
        return $this->hasMany(CoachProfile::class);
    }

    public function programReports(): HasMany
    {
        return $this->hasMany(ProgramReport::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
