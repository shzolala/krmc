<?php

namespace App\Models;

use App\Enums\PlanBadgeColor;
use Database\Factories\ClimbBadgeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'required_count', 'min_elevation', 'color', 'icon', 'sort_order', 'is_active'])]
class ClimbBadge extends Model
{
    /** @use HasFactory<ClimbBadgeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'required_count' => 'integer',
            'min_elevation' => 'integer',
            'color' => PlanBadgeColor::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Human readable requirement, e.g. "۵ قله" or "۳ قله بالای ۴۰۰۰ متر".
     */
    public function requirementText(): string
    {
        $text = fa_num($this->required_count).' قله';

        return $this->min_elevation ? $text.' بالای '.fa_num($this->min_elevation).' متر' : $text;
    }
}
