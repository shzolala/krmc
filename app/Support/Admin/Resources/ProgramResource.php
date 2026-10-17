<?php

namespace App\Support\Admin\Resources;

use App\Enums\ProgramStatus;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\Program;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ProgramResource extends AdminResource
{
    public function model(): string
    {
        return Program::class;
    }

    public function key(): string
    {
        return 'programs';
    }

    public function singular(): string
    {
        return 'برنامه';
    }

    public function plural(): string
    {
        return 'برنامه‌ها';
    }

    public function icon(): string
    {
        return 'calendar-days';
    }

    public function group(): string
    {
        return 'activity';
    }

    protected function publicRoute(): ?string
    {
        return 'programs.show';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'slug'];
    }

    public function with(): array
    {
        return ['club:id,name', 'destination:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->club?->name;
    }

    public function filters(): array
    {
        return [
            'status' => ['label' => 'وضعیت', 'options' => $this->enumOptions(ProgramStatus::class)],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان برنامه'),
            Column::badge('status', 'وضعیت'),
            Column::text('destination.name', 'مقصد'),
            Column::date('starts_at', 'شروع'),
            Column::text('registered_count', 'ثبت‌نام'),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('title', 'عنوان برنامه')->required(),
                $this->slugField('title'),
                Field::select('status', 'وضعیت', $this->enumOptions(ProgramStatus::class))->required(),
                Field::text('summary', 'خلاصه')->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'برگزارکننده' => [
                Field::relation('destination_id', 'مقصد', Destination::class)->required(),
                Field::relation('club_id', 'باشگاه', Club::class)->required(),
                Field::relation('leader_id', 'سرپرست برنامه', CoachProfile::class, 'slug'),
            ],
            'زمان‌بندی و ظرفیت' => [
                Field::datetime('starts_at', 'شروع'),
                Field::datetime('ends_at', 'پایان')->rules(['after_or_equal:starts_at']),
                Field::number('capacity', 'ظرفیت'),
                Field::number('registered_count', 'ثبت‌نام‌شده')->default(0),
            ],
            'وضعیت' => [
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
