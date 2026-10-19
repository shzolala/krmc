<?php

namespace App\Support\Admin\Resources;

use App\Models\FundCampaign;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;

class FundCampaignResource extends AdminResource
{
    public function model(): string
    {
        return FundCampaign::class;
    }

    public function key(): string
    {
        return 'campaigns';
    }

    public function singular(): string
    {
        return 'کمپین جذب سرمایه';
    }

    public function plural(): string
    {
        return 'کمپین‌های جذب سرمایه';
    }

    public function icon(): string
    {
        return 'megaphone';
    }

    public function group(): string
    {
        return 'finance';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'description'];
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان کمپین'),
            Column::money('goal_amount', 'هدف'),
            Column::date('ends_at', 'پایان'),
            Column::toggle('is_active', 'فعال'),
        ];
    }

    public function fields(): array
    {
        return [
            'کمپین' => [
                Field::text('title', 'عنوان کمپین')->required(),
                Field::textarea('description', 'توضیحات و هدف از جذب سرمایه'),
                Field::number('goal_amount', 'مبلغ هدف (تومان)')->required(),
                Field::date('ends_at', 'پایان کمپین'),
                Field::toggle('is_active', 'فعال'),
            ],
        ];
    }
}
