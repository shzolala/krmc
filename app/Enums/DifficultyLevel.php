<?php

namespace App\Enums;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Builder;

enum DifficultyLevel: string
{
    case Easy = 'easy';
    case Moderate = 'moderate';
    case Hard = 'hard';

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'مبتدی',
            self::Moderate => 'متوسط',
            self::Hard => 'حرفه‌ای',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Easy => 'مسیرهای کوتاه و بدون نیاز به تجهیزات فنی؛ مناسب اولین تجربه‌ها و خانواده.',
            self::Moderate => 'صعودهای طولانی‌تر و ارتفاع بالاتر؛ نیازمند آمادگی بدنی و تجربه کوهپیمایی.',
            self::Hard => 'قله‌های مرتفع و فنی؛ همراه با سرپرست باتجربه و تجهیزات تخصصی.',
        };
    }

    /**
     * Inclusive range of the 1–5 difficulty grade stored in the trail info.
     *
     * @return array{0: int, 1: int}
     */
    public function gradeRange(): array
    {
        return match ($this) {
            self::Easy => [1, 2],
            self::Moderate => [3, 3],
            self::Hard => [4, 5],
        };
    }

    public static function fromGrade(?int $grade): ?self
    {
        return collect(self::cases())->first(
            fn (self $level): bool => $grade !== null
                && $grade >= $level->gradeRange()[0]
                && $grade <= $level->gradeRange()[1],
        );
    }

    /**
     * @param  Builder<Destination>  $query
     * @return Builder<Destination>
     */
    public function applyTo(Builder $query): Builder
    {
        return $query->whereBetween('trail_info->difficulty->grade_1_5', $this->gradeRange());
    }
}
