<?php

namespace App\Support\Admin\Resources;

use App\Enums\DestinationCategory;
use App\Models\Destination;
use App\Models\MountainRegion;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class DestinationResource extends AdminResource
{
    public function model(): string
    {
        return Destination::class;
    }

    public function key(): string
    {
        return 'destinations';
    }

    public function singular(): string
    {
        return 'مقصد';
    }

    public function plural(): string
    {
        return 'مقاصد (قله‌ها و طبیعت)';
    }

    public function icon(): string
    {
        return 'flag';
    }

    public function group(): string
    {
        return 'geography';
    }

    protected function publicRoute(): ?string
    {
        return 'destinations.show';
    }

    public function searchable(): array
    {
        return ['name', 'name_en', 'region', 'nearest_city'];
    }

    public function with(): array
    {
        return ['mountainRegion:id,name'];
    }

    public function filters(): array
    {
        return [
            'category' => ['label' => 'دسته', 'options' => $this->enumOptions(DestinationCategory::class)],
            'mountain_region_id' => [
                'label' => 'منطقه',
                'options' => MountainRegion::query()->orderBy('name')->pluck('name', 'id')->mapWithKeys(fn ($n, $i): array => [(string) $i => $n])->all(),
            ],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام مقصد'),
            Column::badge('category', 'دسته'),
            Column::text('mountainRegion.name', 'منطقه'),
            Column::number('elevation', 'ارتفاع (متر)'),
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
                $this->catalogIdField('dest'),
                Field::select('category', 'دسته', $this->enumOptions(DestinationCategory::class)),
                Field::text('subtype', 'زیرنوع'),
                Field::relation('mountain_region_id', 'منطقه کوهستانی', MountainRegion::class),
                Field::tags('alt_names', 'نام‌های دیگر'),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::toggle('description_is_short', 'توضیحات کوتاه است'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'موقعیت' => [
                Field::text('region', 'ناحیه'),
                Field::text('province', 'استان'),
                Field::text('county', 'شهرستان'),
                Field::text('nearest_city', 'نزدیک‌ترین شهر'),
                Field::text('nearest_village', 'نزدیک‌ترین روستا'),
                Field::number('elevation', 'ارتفاع (متر)'),
                ...$this->geoFields(),
            ],
            'مسیر و داده‌های تکمیلی' => [
                Field::json('trail_info', 'اطلاعات مسیر'),
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
