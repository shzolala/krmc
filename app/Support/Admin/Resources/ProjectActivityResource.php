<?php

namespace App\Support\Admin\Resources;

use App\Models\ProjectActivity;
use App\Models\SupportProject;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ProjectActivityResource extends AdminResource
{
    public function model(): string
    {
        return ProjectActivity::class;
    }

    public function key(): string
    {
        return 'project-activities';
    }

    public function singular(): string
    {
        return 'اقدام طرح';
    }

    public function plural(): string
    {
        return 'اقدامات طرح‌ها';
    }

    public function icon(): string
    {
        return 'clipboard-document-check';
    }

    public function group(): string
    {
        return 'projects';
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
        return ['project:id,title'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->project?->title;
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'اقدام'),
            Column::text('project.title', 'طرح'),
            Column::date('happened_on', 'تاریخ'),
        ];
    }

    public function fields(): array
    {
        return [
            'اقدام انجام‌شده توسط جامعه کوهنوردان' => [
                Field::relation('support_project_id', 'طرح حمایتی', SupportProject::class, 'title')->required(),
                Field::text('title', 'عنوان اقدام')->required(),
                Field::date('happened_on', 'تاریخ'),
                Field::textarea('description', 'شرح'),
            ],
        ];
    }
}
