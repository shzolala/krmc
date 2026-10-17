<?php

namespace App\Support\Admin\Resources;

use App\Models\Destination;
use App\Models\Shelter;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class ShelterResource extends AdminResource
{
    public function model(): string
    {
        return Shelter::class;
    }

    public function key(): string
    {
        return 'shelters';
    }

    public function singular(): string
    {
        return 'پناهگاه';
    }

    public function plural(): string
    {
        return 'پناهگاه‌ها';
    }

    public function icon(): string
    {
        return 'home';
    }

    public function group(): string
    {
        return 'geography';
    }

    protected function publicRoute(): ?string
    {
        return 'shelters.show';
    }

    public function with(): array
    {
        return ['destination:id,name'];
    }

    public function filters(): array
    {
        return [
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام پناهگاه'),
            Column::text('destination.name', 'مقصد مرتبط'),
            Column::number('elevation', 'ارتفاع (متر)'),
            Column::number('capacity', 'ظرفیت'),
            Column::number('view_count', 'بازدید'),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('name', 'نام پناهگاه')->required(),
                $this->slugField(),
                Field::relation('destination_id', 'مقصد مرتبط', Destination::class),
                Field::number('elevation', 'ارتفاع (متر)'),
                Field::number('capacity', 'ظرفیت (نفر)'),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::textarea('description', 'توضیحات'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'موقعیت' => $this->geoFields(),
            'وضعیت' => [
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
