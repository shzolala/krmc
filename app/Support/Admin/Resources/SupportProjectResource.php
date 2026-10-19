<?php

namespace App\Support\Admin\Resources;

use App\Enums\ProjectCategory;
use App\Enums\ProjectStatus;
use App\Models\SupportProject;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class SupportProjectResource extends AdminResource
{
    public function model(): string
    {
        return SupportProject::class;
    }

    public function key(): string
    {
        return 'projects';
    }

    public function singular(): string
    {
        return 'طرح حمایتی';
    }

    public function plural(): string
    {
        return 'طرح‌های حمایتی';
    }

    public function icon(): string
    {
        return 'rocket-launch';
    }

    public function group(): string
    {
        return 'projects';
    }

    protected function publicRoute(): ?string
    {
        return 'projects.show';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'slug', 'summary'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->category->label();
    }

    public function filters(): array
    {
        return [
            'category' => ['label' => 'دسته‌بندی', 'options' => $this->enumOptions(ProjectCategory::class)],
            'status' => ['label' => 'وضعیت', 'options' => $this->enumOptions(ProjectStatus::class)],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان طرح'),
            Column::badge('category', 'دسته‌بندی'),
            Column::badge('status', 'وضعیت'),
            Column::money('budget', 'بودجه'),
            Column::toggle('is_featured', 'ویژه'),
        ];
    }

    public function fields(): array
    {
        return [
            'معرفی طرح' => [
                Field::text('title', 'عنوان طرح')->required(),
                $this->slugField('title'),
                Field::select('category', 'دسته‌بندی (هدف هزینه‌کرد)', $this->enumOptions(ProjectCategory::class))->required(),
                Field::select('status', 'وضعیت', $this->enumOptions(ProjectStatus::class))->required(),
                Field::text('summary', 'معرفی کوتاه', 500)->wide(),
                Field::textarea('description', 'توضیحات کامل'),
                Field::text('cover_path', 'تصویر (مسیر یا نشانی)'),
            ],
            'بودجه و زمان‌بندی' => [
                Field::number('budget', 'بودجه‌ی هدف (تومان)')->required()
                    ->hint('جذب مالی (نقدی و غیرنقدی) نسبت به این بودجه سنجیده و نمایش داده می‌شود.'),
                Field::date('starts_on', 'شروع'),
                Field::date('ends_on', 'پایان'),
            ],
            'نمایش' => [
                Field::toggle('is_featured', 'ویژه'),
                Field::number('sort_order', 'ترتیب نمایش')->default(0),
            ],
        ];
    }
}
