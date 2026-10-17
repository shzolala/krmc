<?php

namespace App\Support\Admin\Resources;

use App\Enums\AttractionType;
use App\Models\Attraction;
use App\Models\MountainRegion;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class AttractionResource extends AdminResource
{
    public function model(): string
    {
        return Attraction::class;
    }

    public function key(): string
    {
        return 'attractions';
    }

    public function singular(): string
    {
        return 'جاذبه';
    }

    public function plural(): string
    {
        return 'جاذبه‌های گردشگری';
    }

    public function icon(): string
    {
        return 'building-library';
    }

    public function group(): string
    {
        return 'geography';
    }

    protected function publicRoute(): ?string
    {
        return 'attractions.show';
    }

    public function searchable(): array
    {
        return ['name', 'name_en', 'city', 'era'];
    }

    public function filters(): array
    {
        return [
            'type' => ['label' => 'نوع', 'options' => $this->enumOptions(AttractionType::class)],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام جاذبه'),
            Column::badge('type', 'نوع'),
            Column::text('city', 'شهر', true),
            Column::text('era', 'دوره', true),
            Column::number('view_count', 'بازدید'),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('name', 'نام')->required(),
                Field::text('name_en', 'نام انگلیسی'),
                $this->slugField(),
                $this->catalogIdField('attr'),
                Field::select('type', 'نوع', $this->enumOptions(AttractionType::class))->required(),
                Field::tags('types', 'انواع دیگر'),
                Field::text('era', 'دوره تاریخی'),
                Field::relation('mountain_region_id', 'منطقه کوهستانی', MountainRegion::class),
                Field::tags('alt_names', 'نام‌های دیگر'),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'موقعیت' => [
                Field::text('province', 'استان'),
                Field::text('county', 'شهرستان'),
                Field::text('city', 'شهر'),
                Field::decimal('latitude', 'عرض جغرافیایی')->rules(['between:-90,90']),
                Field::decimal('longitude', 'طول جغرافیایی')->rules(['between:-180,180']),
            ],
            'داده‌های تکمیلی' => [
                Field::json('unesco', 'یونسکو'),
                Field::json('visiting', 'اطلاعات بازدید'),
                Field::json('extras', 'موارد تکمیلی'),
                Field::json('seo', 'سئو'),
                Field::json('sources', 'منابع'),
                Field::json('coordinate_meta', 'متادیتای مختصات'),
                Field::json('data_confidence', 'اطمینان داده'),
            ],
            'وضعیت' => [
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
