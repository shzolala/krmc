<?php

namespace App\Models;

use App\Enums\ProjectCategory;
use App\Enums\SponsorStatus;
use Database\Factories\SponsorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'sponsorship_package_id',
    'name',
    'slug',
    'logo_path',
    'website',
    'summary',
    'description',
    'support_note',
    'preferred_category',
    'support_project_id',
    'pledge_amount',
    'show_amount',
    'status',
    'is_featured',
    'sort_order',
    'started_at',
])]
class Sponsor extends Model
{
    /** @use HasFactory<SponsorFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SponsorStatus::class,
            'preferred_category' => ProjectCategory::class,
            'pledge_amount' => 'integer',
            'show_amount' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'started_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(SponsorshipPackage::class, 'sponsorship_package_id');
    }

    public function preferredProject(): BelongsTo
    {
        return $this->belongsTo(SupportProject::class, 'support_project_id');
    }

    /**
     * Projects this sponsor has supported, each with its contributions (round, cash or in kind).
     *
     * @return Collection<int, array{project: SupportProject, total: int, contributions: Collection<int, FundIncome>}>
     */
    public function supportedProjects(): Collection
    {
        return $this->incomes()
            ->whereNotNull('support_project_id')
            ->with('project')
            ->orderBy('installment_no')
            ->orderBy('received_at')
            ->get()
            ->groupBy('support_project_id')
            ->map(fn ($contributions): array => [
                'project' => $contributions->first()->project,
                'total' => (int) $contributions->sum('amount'),
                'contributions' => $contributions->values(),
            ])
            ->sortByDesc('total')
            ->values();
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(FundIncome::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function visible(Builder $query): void
    {
        $query->where('status', SponsorStatus::Active->value);
    }

    public function paidAmount(): int
    {
        return (int) ($this->incomes_sum_amount ?? $this->incomes()->sum('amount'));
    }

    /**
     * Resolves a stored logo to a URL: absolute URLs and public asset paths are used as-is,
     * anything else is read from the public disk (panel uploads).
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->logo_path;

            return match (true) {
                blank($path) => null,
                str_starts_with($path, 'http'), str_starts_with($path, '/') => $path,
                str_starts_with($path, 'images/') => asset($path),
                default => Storage::disk('public')->url($path),
            };
        });
    }

    protected function paidTotal(): Attribute
    {
        return Attribute::get(fn (): int => $this->paidAmount());
    }
}
