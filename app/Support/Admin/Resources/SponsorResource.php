<?php

namespace App\Support\Admin\Resources;

use App\Enums\ProjectCategory;
use App\Enums\SponsorStatus;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use App\Models\SupportProject;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class SponsorResource extends AdminResource
{
    public function model(): string
    {
        return Sponsor::class;
    }

    public function key(): string
    {
        return 'sponsors';
    }

    public function singular(): string
    {
        return 'حامی';
    }

    public function plural(): string
    {
        return 'حامیان کوهستان';
    }

    public function icon(): string
    {
        return 'building-library';
    }

    public function group(): string
    {
        return 'finance';
    }

    protected function publicRoute(): ?string
    {
        return 'sponsors.show';
    }

    public function searchable(): array
    {
        return ['name', 'slug', 'summary'];
    }

    public function with(): array
    {
        return ['package:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->package?->name;
    }

    public function filters(): array
    {
        return [
            'status' => ['label' => 'وضعیت', 'options' => $this->enumOptions(SponsorStatus::class)],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام حامی'),
            Column::badge('status', 'وضعیت'),
            Column::money('pledge_amount', 'تعهد'),
            Column::money('paid_total', 'پرداخت‌شده', false),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'معرفی حامی' => [
                Field::text('name', 'نام شرکت یا مجموعه')->required(),
                $this->slugField('name'),
                Field::text('logo_path', 'لوگو (مسیر، نشانی یا آپلود خود حامی)')
                    ->hint('مثلاً images/sponsors/logo.svg یا یک نشانی کامل.'),
                Field::text('website', 'وب‌سایت')->rules(['url']),
                Field::text('summary', 'معرفی کوتاه', 500)->wide(),
                Field::textarea('description', 'معرفی کامل'),
                Field::textarea('support_note', 'حمایتی که انجام داده یا انجام می‌دهد'),
            ],
            'حمایت مالی' => [
                Field::select('preferred_category', 'هدف حمایت (دسته‌بندی)', $this->enumOptions(ProjectCategory::class)),
                Field::relation('support_project_id', 'طرح مورد نظر حامی', SupportProject::class, 'title'),
                Field::relation('user_id', 'حساب کاربری حامی (در صورت وجود)', User::class),
                Field::relation('sponsorship_package_id', 'بسته حمایت', SponsorshipPackage::class),
                Field::number('pledge_amount', 'مبلغ تعهد (تومان)')->default(0)
                    ->hint('پرداخت‌های واقعی از بخش «دریافتی‌های صندوق» با انتخاب همین حامی ثبت می‌شود.'),
                Field::toggle('show_amount', 'نمایش مبلغ حمایت در سایت'),
                Field::date('started_at', 'شروع حمایت'),
            ],
            'نمایش' => [
                Field::select('status', 'وضعیت', $this->enumOptions(SponsorStatus::class))->required(),
                Field::toggle('is_featured', 'ویژه (نمایش در ابتدای فهرست)'),
                Field::number('sort_order', 'ترتیب نمایش')->default(0),
            ],
        ];
    }
}
