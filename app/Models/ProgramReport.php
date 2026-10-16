<?php

namespace App\Models;

use Database\Factories\ProgramReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Review\Models\Concerns\HasReviews;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'body',
    'cover_path',
    'destination_id',
    'club_id',
    'author_id',
    'published_at',
    'view_count',
])]
class ProgramReport extends Model
{
    /** @use HasFactory<ProgramReportFactory> */
    use HasFactory;

    use HasReviews;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
