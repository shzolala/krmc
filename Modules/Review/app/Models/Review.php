<?php

namespace Modules\Review\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Review\Database\Factories\ReviewFactory;
use Modules\Review\Enums\ReviewStatus;

#[Fillable([
    'user_id',
    'reviewable_type',
    'reviewable_id',
    'rating',
    'title',
    'body',
    'status',
    'moderated_by',
    'moderated_at',
])]
class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
            'moderated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): ReviewFactory
    {
        return ReviewFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', ReviewStatus::Approved);
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', ReviewStatus::Pending);
    }

    public function isPending(): bool
    {
        return $this->status === ReviewStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === ReviewStatus::Approved;
    }
}
