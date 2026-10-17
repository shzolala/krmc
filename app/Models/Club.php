<?php

namespace App\Models;

use App\Models\Concerns\HasGeoLocation;
use App\Support\CatalogCover;
use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'farakouh_id',
    'farakouh_url',
    'name',
    'slug',
    'summary',
    'description',
    'city',
    'province',
    'latitude',
    'longitude',
    'address',
    'manager',
    'education_head',
    'avg_rating',
    'contact',
    'courses_summary',
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
            'farakouh_id' => 'integer',
            'avg_rating' => 'integer',
            'contact' => 'array',
            'courses_summary' => 'array',
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

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function programReports(): HasMany
    {
        return $this->hasMany(ProgramReport::class);
    }

    public function getCoverPathAttribute(?string $value): string
    {
        return $value ?: CatalogCover::for('club-'.($this->slug ?? (string) $this->id));
    }

    /**
     * @return list<string>
     */
    public function topSpecialties(int $limit = 2): array
    {
        $courses = $this->courses_summary['top_course_types'] ?? null;
        if (is_array($courses) && count($courses) > 0) {
            return array_slice(array_keys($courses), 0, $limit);
        }

        return ['کوه‌پیمایی', 'طبیعت‌گردی'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
