<?php

namespace App\Support\Admin\Resources;

use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\MountainRegion;
use App\Models\Program;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class HeroSlideResource extends AdminResource
{
    public function model(): string
    {
        return HeroSlide::class;
    }

    public function key(): string
    {
        return 'hero-slides';
    }

    public function singular(): string
    {
        return 'اسلاید صفحه اول';
    }

    public function plural(): string
    {
        return 'اسلایدر صفحه اول';
    }

    public function icon(): string
    {
        return 'photo';
    }

    public function group(): string
    {
        return 'site';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'subtitle'];
    }

    public function with(): array
    {
        return ['mountainRegion', 'destination', 'program'];
    }

    public function secondaryText(Model $record): ?string
    {
        $target = $record->target();

        return $target ? $target['kind'].': '.$target['label'] : null;
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان اسلاید'),
            Column::number('sort_order', 'ترتیب'),
            Column::toggle('is_active', 'فعال'),
        ];
    }

    public function fields(): array
    {
        return [
            'تصویر و متن' => [
                Field::image('image_path', 'تصویر اسلاید', 'slides')->required(),
                Field::text('title', 'عنوان')->required(),
                Field::text('subtitle', 'توضیح کوتاه', 300)->wide(),
            ],
            'پیوند نوار پایین اسلاید' => [
                Field::relation('program_id', 'برنامه', Program::class, 'title')
                    ->hint('فقط یکی از سه مورد را انتخاب کنید؛ اگر چند مورد انتخاب شود برنامه، سپس مقصد، سپس منطقه اولویت دارد.'),
                Field::relation('destination_id', 'مقصد', Destination::class),
                Field::relation('mountain_region_id', 'منطقه', MountainRegion::class),
            ],
            'نمایش' => [
                Field::number('sort_order', 'ترتیب نمایش')->default(0),
                Field::toggle('is_active', 'فعال'),
            ],
        ];
    }
}
