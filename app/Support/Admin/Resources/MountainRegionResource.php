<?php

namespace App\Support\Admin\Resources;

use App\Enums\MountainRegionType;
use App\Models\MountainRegion;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class MountainRegionResource extends AdminResource
{
    public function model(): string
    {
        return MountainRegion::class;
    }

    public function key(): string
    {
        return 'regions';
    }

    public function singular(): string
    {
        return 'منطقه کوهستانی';
    }

    public function plural(): string
    {
        return 'مناطق کوهستانی';
    }

    public function icon(): string
    {
        return 'globe-asia-australia';
    }

    public function group(): string
    {
        return 'geography';
    }

    protected function publicRoute(): ?string
    {
        return 'regions.show';
    }

    public function searchable(): array
    {
        return ['name', 'name_en', 'province', 'nearest_city'];
    }

    public function filters(): array
    {
        return [
            'type' => ['label' => 'نوع', 'options' => $this->enumOptions(MountainRegionType::class)],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام منطقه'),
            Column::badge('type', 'نوع'),
            Column::text('province', 'استان', true),
            Column::number('highest_elevation', 'بلندترین نقطه (متر)'),
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
                $this->catalogIdField('region'),
                Field::select('type', 'نوع منطقه', $this->enumOptions(MountainRegionType::class))->required(),
                Field::tags('alt_names', 'نام‌های دیگر'),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'موقعیت' => [
                Field::text('province', 'استان'),
                Field::tags('counties', 'شهرستان‌ها'),
                Field::text('nearest_city', 'نزدیک‌ترین شهر'),
                Field::decimal('latitude', 'عرض جغرافیایی')->rules(['between:-90,90']),
                Field::decimal('longitude', 'طول جغرافیایی')->rules(['between:-180,180']),
                Field::number('highest_elevation', 'بلندترین ارتفاع (متر)'),
                Field::json('bbox', 'محدوده جغرافیایی (bbox)')->hint('کلیدها: min_lat، max_lat، min_lng، max_lng'),
            ],
            'داده‌های تکمیلی' => [
                Field::json('seo', 'سئو'),
                Field::json('notes', 'یادداشت‌ها'),
                Field::json('data_confidence', 'اطمینان داده'),
            ],
            'وضعیت' => [
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
