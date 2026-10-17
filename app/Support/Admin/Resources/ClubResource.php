<?php

namespace App\Support\Admin\Resources;

use App\Models\Club;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ClubResource extends AdminResource
{
    public function model(): string
    {
        return Club::class;
    }

    public function key(): string
    {
        return 'clubs';
    }

    public function singular(): string
    {
        return 'باشگاه';
    }

    public function plural(): string
    {
        return 'باشگاه‌ها';
    }

    public function icon(): string
    {
        return 'building-office-2';
    }

    public function group(): string
    {
        return 'directory';
    }

    protected function publicRoute(): ?string
    {
        return 'clubs.show';
    }

    public function searchable(): array
    {
        return ['name', 'city', 'manager'];
    }

    public function with(): array
    {
        return ['owner:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->owner?->name ? 'مالک: '.$record->owner->name : null;
    }

    public function filters(): array
    {
        return [
            'is_verified' => ['label' => 'تأیید هویت', 'options' => $this->yesNo()],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
            'is_premium' => ['label' => 'پریمیوم', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام باشگاه'),
            Column::text('city', 'شهر', true),
            Column::number('member_count', 'اعضا'),
            Column::number('program_count', 'برنامه‌ها'),
            Column::toggle('is_verified', 'تأیید'),
            Column::toggle('is_featured', 'ویژه'),
            Column::toggle('is_premium', 'پریمیوم'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('name', 'نام باشگاه')->required(),
                $this->slugField(),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::number('founded_year', 'سال تأسیس'),
                Field::relation('owner_id', 'مالک (کاربر)', User::class),
            ],
            'موقعیت' => [
                Field::text('city', 'شهر')->required(),
                Field::text('province', 'استان'),
                ...$this->geoFields(),
            ],
            'مدیریت و تماس' => [
                Field::text('manager', 'مدیر باشگاه'),
                Field::text('education_head', 'مسئول آموزش'),
                Field::json('contact', 'اطلاعات تماس'),
                Field::json('courses_summary', 'خلاصه دوره‌ها'),
            ],
            'رسانه' => [
                Field::text('logo_path', 'مسیر / نشانی لوگو'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'وضعیت و آمار' => [
                Field::toggle('is_verified', 'تأیید هویت شده'),
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::toggle('is_premium', 'عضویت پریمیوم'),
                Field::number('member_count', 'تعداد اعضا')->default(0),
                Field::number('program_count', 'تعداد برنامه‌ها')->default(0),
                Field::number('avg_rating', 'میانگین امتیاز'),
                Field::number('farakouh_id', 'شناسه فراکوه')->unique(),
                Field::text('farakouh_url', 'پیوند فراکوه'),
            ],
        ];
    }
}
