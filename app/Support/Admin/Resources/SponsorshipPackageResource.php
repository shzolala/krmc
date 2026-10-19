<?php

namespace App\Support\Admin\Resources;

use App\Models\SponsorshipPackage;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class SponsorshipPackageResource extends AdminResource
{
    public function model(): string
    {
        return SponsorshipPackage::class;
    }

    public function key(): string
    {
        return 'sponsorship-packages';
    }

    public function singular(): string
    {
        return 'بسته حمایت';
    }

    public function plural(): string
    {
        return 'بسته‌های حمایت';
    }

    public function icon(): string
    {
        return 'gift';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function searchable(): array
    {
        return ['name', 'description'];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام بسته'),
            Column::money('min_amount', 'حداقل مبلغ'),
            Column::number('sort_order', 'ترتیب'),
            Column::toggle('is_active', 'فعال'),
        ];
    }

    public function fields(): array
    {
        return [
            'بسته حمایت' => [
                Field::text('name', 'نام بسته (مثلاً حامی طلایی کوهستان)')->required(),
                Field::number('min_amount', 'حداقل مبلغ حمایت (تومان)')->required()
                    ->hint('حامی نمی‌تواند کمتر از این مبلغ تعهد دهد. کمترین مبلغ بسته‌های فعال، حداقل کل حمایت است.'),
                Field::number('sort_order', 'ترتیب نمایش')->default(0),
                Field::textarea('description', 'توضیح کوتاه'),
                Field::tags('benefits', 'مزایا و امکانات تبلیغاتی')->wide(),
                Field::toggle('is_active', 'فعال'),
            ],
        ];
    }
}
