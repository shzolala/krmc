<?php

namespace App\Support\Admin\Resources;

use App\Enums\FundExpenseCategory;
use App\Models\FundCampaign;
use App\Models\FundExpense;
use App\Models\SupportProject;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class FundExpenseResource extends AdminResource
{
    public function model(): string
    {
        return FundExpense::class;
    }

    public function key(): string
    {
        return 'expenses';
    }

    public function singular(): string
    {
        return 'هزینه';
    }

    public function plural(): string
    {
        return 'هزینه‌های صندوق';
    }

    public function icon(): string
    {
        return 'arrow-up-tray';
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

    public function with(): array
    {
        return ['campaign:id,title'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->campaign?->title;
    }

    public function filters(): array
    {
        return [
            'category' => ['label' => 'دسته‌بندی', 'options' => $this->enumOptions(FundExpenseCategory::class)],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'شرح هزینه'),
            Column::badge('category', 'دسته‌بندی'),
            Column::money('amount', 'مبلغ'),
            Column::date('spent_at', 'تاریخ هزینه'),
        ];
    }

    public function fields(): array
    {
        return [
            'هزینه' => [
                Field::text('title', 'شرح هزینه')->required(),
                Field::select('category', 'دسته‌بندی', $this->enumOptions(FundExpenseCategory::class))->required(),
                Field::number('amount', 'مبلغ (تومان)')->required(),
                Field::date('spent_at', 'تاریخ هزینه')->required(),
                Field::relation('fund_campaign_id', 'کمپین مرتبط', FundCampaign::class, 'title'),
                Field::relation('support_project_id', 'طرح حمایتی مرتبط', SupportProject::class, 'title'),
                Field::textarea('description', 'توضیحات و مستندات'),
            ],
        ];
    }
}
