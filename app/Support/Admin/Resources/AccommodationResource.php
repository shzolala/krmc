<?php

namespace App\Support\Admin\Resources;

use App\Enums\LodgingType;
use App\Models\Accommodation;
use App\Models\Destination;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class AccommodationResource extends AdminResource
{
    public function model(): string
    {
        return Accommodation::class;
    }

    public function key(): string
    {
        return 'accommodations';
    }

    public function singular(): string
    {
        return 'اقامتگاه';
    }

    public function plural(): string
    {
        return 'اقامتگاه‌ها';
    }

    public function icon(): string
    {
        return 'home-modern';
    }

    public function group(): string
    {
        return 'geography';
    }

    protected function publicRoute(): ?string
    {
        return 'accommodations.show';
    }

    public function searchable(): array
    {
        return ['name', 'city'];
    }

    public function with(): array
    {
        return ['destination:id,name'];
    }

    public function filters(): array
    {
        return [
            'type' => ['label' => 'نوع', 'options' => $this->enumOptions(LodgingType::class)],
            'is_verified' => ['label' => 'تأیید', 'options' => $this->yesNo()],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام اقامتگاه'),
            Column::badge('type', 'نوع'),
            Column::text('city', 'شهر', true),
            Column::text('destination.name', 'مقصد مرتبط'),
            Column::toggle('is_verified', 'تأیید'),
            Column::toggle('is_featured', 'ویژه'),
            Column::toggle('is_premium', 'پریمیوم'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('name', 'نام اقامتگاه')->required(),
                $this->slugField(),
                Field::select('type', 'نوع', $this->enumOptions(LodgingType::class))->required(),
                Field::relation('destination_id', 'مقصد مرتبط', Destination::class),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'موقعیت' => [
                Field::text('city', 'شهر')->required(),
                ...$this->geoFields(),
            ],
            'وضعیت' => [
                Field::toggle('is_verified', 'تأیید شده'),
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::toggle('is_premium', 'پریمیوم'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
