<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'slug',
    'summary',
    'description',
    'status',
    'destination_id',
    'club_id',
    'leader_id',
    'starts_at',
    'ends_at',
    'capacity',
    'registered_count',
    'cover_path',
    'is_featured',
    'view_count',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'upcoming',
        'registered_count' => 0,
        'is_featured' => false,
        'view_count' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProgramStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(CoachProfile::class, 'leader_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function ongoing(Builder $query): Builder
    {
        return $query->where('status', ProgramStatus::Ongoing);
    }

    /**
     * Upcoming and currently running programs.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ProgramStatus::Upcoming,
            ProgramStatus::Ongoing,
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
