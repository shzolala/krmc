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
    'user_id',
    'club_id',
    'slug',
    'headline',
    'bio',
    'specialty',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function roleLabel(): string
    {
        return $this->is_leader ? 'سرپرست' : 'مربی';
    }
}
