<?php

namespace App\Support\Admin\Resources;

use App\Models\Club;
use App\Models\Destination;
use App\Models\ProgramReport;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ProgramReportResource extends AdminResource
{
    public function model(): string
    {
        return ProgramReport::class;
    }

    public function key(): string
    {
        return 'reports';
    }

    public function singular(): string
    {
        return 'گزارش برنامه';
    }

    public function plural(): string
    {
        return 'گزارش برنامه‌ها';
    }

    public function icon(): string
    {
        return 'document-text';
    }

    public function group(): string
    {
        return 'activity';
    }

    public function titleAttribute(): string
    {
        return 'title';
    }

    public function searchable(): array
    {
        return ['title', 'slug', 'excerpt'];
    }

    public function with(): array
    {
        return ['club:id,name', 'author:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->author?->name ? 'نویسنده: '.$record->author->name : null;
    }

    public function columns(): array
    {
        return [
            Column::title('title', 'عنوان گزارش'),
            Column::text('club.name', 'باشگاه'),
            Column::date('published_at', 'انتشار'),
            Column::number('view_count', 'بازدید'),
        ];
    }

    public function fields(): array
    {
        return [
            'محتوا' => [
                Field::text('title', 'عنوان')->required(),
                $this->slugField('title'),
                Field::text('excerpt', 'چکیده', 500)->wide(),
                Field::textarea('body', 'متن گزارش'),
                Field::text('cover_path', 'مسیر / نشانی کاور'),
            ],
            'ارتباط‌ها' => [
                Field::relation('destination_id', 'مقصد', Destination::class),
                Field::relation('club_id', 'باشگاه', Club::class),
                Field::relation('author_id', 'نویسنده', User::class),
            ],
            'انتشار' => [
                Field::datetime('published_at', 'زمان انتشار')->hint('خالی بودن یعنی پیش‌نویس.'),
                Field::number('view_count', 'تعداد بازدید')->default(0),
            ],
        ];
    }
}
