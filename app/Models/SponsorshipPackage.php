<?php

namespace App\Models;

use Database\Factories\SponsorshipPackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'min_amount', 'description', 'benefits', 'sort_order', 'is_active'])]
class SponsorshipPackage extends Model
{
    /** @use HasFactory<SponsorshipPackageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_amount' => 'integer',
            'benefits' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * The lowest amount anyone may pledge; zero when no package is defined.
     */
    public static function minimumPledge(): int
    {
        return (int) static::query()->active()->min('min_amount');
    }
}
