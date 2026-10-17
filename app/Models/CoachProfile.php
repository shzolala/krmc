<?php

namespace App\Models;

use Database\Factories\CoachProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'farakouh_id',
    'farakouh_url',
    'user_id',
    'club_id',
    'slug',
    'headline',
    'bio',
    'specialty',
    'province',
    'avg_rating',
    'total_courses_taught',
    'certificates',
    'contact',
    'courses_summary',
    'avatar_path',
    'experience_years',
    'is_verified',
    'is_featured',
    'is_premium',
    'is_leader',
])]
class CoachProfile extends Model
{
    /** @use HasFactory<CoachProfileFactory> */
    use HasFactory;

    use HasReviews;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'farakouh_id' => 'integer',
            'avg_rating' => 'integer',
            'total_courses_taught' => 'integer',
            'certificates' => 'array',
            'contact' => 'array',
            'courses_summary' => 'array',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_premium' => 'boolean',
            'is_leader' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function destinationGuides(): HasMany
    {
        return $this->hasMany(DestinationGuide::class);
    }

    public function ledPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'leader_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function roleLabel(): string
    {
        return $this->is_leader ? 'سرپرست' : 'مربی';
    }
}
