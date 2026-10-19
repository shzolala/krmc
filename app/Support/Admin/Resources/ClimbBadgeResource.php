<?php

namespace App\Support\Admin\Resources;

use App\Enums\PlanBadgeColor;
use App\Models\ClimbBadge;
use App\Models\MembershipPlan;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ClimbBadgeResource extends AdminResource
{
    public function model(): string
    {
        return ClimbBadge::class;
    }

    public function key(): string
    {
        return 'climb-badges';
    }

    public function singular(): string
    {
        return 'نشان صعود';
    }

    public function plural(): string
    {
        return 'نشان‌های صعود';
    }

    public function icon(): string
    {
        return 'trophy';
    }

    public function group(): string
    {
        return 'peaks';
    }

    public function searchable(): array
    {
        return ['name', 'description'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->requirementText();
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام نشان'),
            Column::number('required_count', 'تعداد قله'),
            Column::number('min_elevation', 'حداقل ارتفاع (م)'),
            Column::badge('color', 'رنگ'),
            Column::number('sort_order', 'ترتیب'),
            Column::toggle('is_active', 'فعال'),
        ];
    }

    public function fields(): array
    {
        return [
            'شرط دریافت نشان' => [
                Field::text('name', 'نام نشان (مثلاً چهارهزارنورد)')->required(),
                Field::text('description', 'توضیح کوتاه')->wide(),
                Field::number('required_count', 'تعداد قله‌ی لازم')->required()->rules(['min:1']),
                Field::number('min_elevation', 'فقط قله‌های بالای … متر')
                    ->hint('خالی بگذارید تا هر قله‌ای حساب شود؛ مثلاً ۴۰۰۰ یعنی فقط قله‌های بالای ۴۰۰۰ متر.'),
            ],
            'ظاهر و نمایش' => [
                Field::select('color', 'رنگ', $this->enumOptions(PlanBadgeColor::class))->required(),
                Field::select('icon', 'آیکون', array_combine(MembershipPlan::BADGE_ICONS, MembershipPlan::BADGE_ICONS))->required(),
                Field::number('sort_order', 'ترتیب نمایش')->default(0),
                Field::toggle('is_active', 'فعال'),
            ],
        ];
    }
}
