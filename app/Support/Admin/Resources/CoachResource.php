<?php

namespace App\Support\Admin\Resources;

use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class CoachResource extends AdminResource
{
    public function model(): string
    {
        return CoachProfile::class;
    }

    public function key(): string
    {
        return 'coaches';
    }

    public function singular(): string
    {
        return 'پروفایل مربی / سرپرست';
    }

    public function plural(): string
    {
        return 'مربیان و سرپرستان';
    }

    public function icon(): string
    {
        return 'academic-cap';
    }

    public function group(): string
    {
        return 'directory';
    }

    protected function publicRoute(): ?string
    {
        return 'coaches.show';
    }

    public function titleAttribute(): string
    {
        return 'headline';
    }

    public function recordTitle(Model $record): string
    {
        return $record->user?->name ?? (string) $record->slug;
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->headline ?: $record->specialty;
    }

    public function searchable(): array
    {
        return ['headline', 'specialty', 'slug'];
    }

    public function with(): array
    {
        return ['user:id,name', 'club:id,name'];
    }

    public function filters(): array
    {
        return [
            'is_leader' => ['label' => 'نوع', 'options' => ['1' => 'سرپرست', '0' => 'مربی']],
            'is_verified' => ['label' => 'تأیید هویت', 'options' => $this->yesNo()],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('user.name', 'نام', false),
            Column::text('specialty', 'تخصص', true),
            Column::text('club.name', 'باشگاه'),
            Column::number('experience_years', 'سابقه (سال)'),
            Column::toggle('is_leader', 'سرپرست'),
            Column::toggle('is_verified', 'تأیید'),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::relation('user_id', 'کاربر', User::class)->required(),
                Field::relation('club_id', 'باشگاه', Club::class),
                $this->slugField('headline'),
                Field::text('headline', 'عنوان / سمت'),
                Field::text('specialty', 'تخصص'),
                Field::text('province', 'استان'),
                Field::textarea('bio', 'درباره'),
            ],
            'سوابق' => [
                Field::number('experience_years', 'سال سابقه'),
                Field::number('total_courses_taught', 'تعداد دوره‌های تدریس‌شده')->default(0),
                Field::tags('certificates', 'مدارک و گواهینامه‌ها'),
                Field::json('contact', 'اطلاعات تماس'),
                Field::json('courses_summary', 'خلاصه دوره‌ها'),
            ],
            'رسانه' => [
                Field::text('avatar_path', 'مسیر / نشانی تصویر'),
            ],
            'وضعیت' => [
                Field::toggle('is_leader', 'سرپرست برنامه (نه فقط مربی)'),
                Field::toggle('is_verified', 'تأیید هویت شده'),
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::toggle('is_premium', 'عضویت پریمیوم'),
                Field::number('avg_rating', 'میانگین امتیاز'),
                Field::number('farakouh_id', 'شناسه فراکوه')->unique(),
                Field::text('farakouh_url', 'پیوند فراکوه'),
            ],
        ];
    }
}
