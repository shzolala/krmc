<?php

namespace App\Models;

use Database\Factories\HeroSlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'subtitle',
    'image_path',
    'mountain_region_id',
    'destination_id',
    'program_id',
    'sort_order',
    'is_active',
])]
class HeroSlide extends Model
{
    /** @use HasFactory<HeroSlideFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function mountainRegion(): BelongsTo
    {
        return $this->belongsTo(MountainRegion::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Public asset paths (images/...) are used as-is, anything else is read from the public disk (admin uploads).
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (): string {
            $path = $this->image_path;

            return match (true) {
                str_starts_with($path, 'http'), str_starts_with($path, '/') => $path,
                str_starts_with($path, 'images/') => asset($path),
                default => Storage::disk('public')->url($path),
            };
        });
    }

    /**
     * The on-site page this slide points to: a program, a destination or a region (first one set wins).
     *
     * @return array{kind: string, label: string, url: string}|null
     */
    public function target(): ?array
    {
        return match (true) {
            $this->program !== null => ['kind' => 'برنامه', 'label' => $this->program->title, 'url' => route('programs.show', $this->program)],
            $this->destination !== null => ['kind' => 'مقصد', 'label' => $this->destination->name, 'url' => route('destinations.show', $this->destination)],
            $this->mountainRegion !== null => ['kind' => 'منطقه', 'label' => $this->mountainRegion->name, 'url' => route('regions.show', $this->mountainRegion)],
            default => null,
        };
    }
}
