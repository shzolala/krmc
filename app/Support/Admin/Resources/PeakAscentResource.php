<?php

namespace App\Support\Admin\Resources;

use App\Models\Destination;
use App\Models\PeakAscent;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PeakAscentResource extends AdminResource
{
    public function model(): string
    {
        return PeakAscent::class;
    }

    public function key(): string
    {
        return 'peak-ascents';
    }

    public function singular(): string
    {
        return 'صعود ثبت‌شده';
    }

    public function plural(): string
    {
        return 'صعودهای ثبت‌شده';
    }

    public function icon(): string
    {
        return 'check-badge';
    }

    public function group(): string
    {
        return 'peaks';
    }

    public function titleAttribute(): string
    {
        return 'note';
    }

    public function recordTitle(Model $record): string
    {
        return ($record->user?->name ?? '—').' — '.($record->destination?->name ?? '—');
    }

    public function searchable(): array
    {
        return ['note'];
    }

    public function with(): array
    {
        return ['user:id,name', 'destination:id,name'];
    }

    public function columns(): array
    {
        return [
            Column::title('note', 'صعود', false),
            Column::text('destination.name', 'قله'),
            Column::date('climbed_on', 'تاریخ صعود'),
        ];
    }

    public function fields(): array
    {
        return [
            'صعود' => [
                Field::relation('user_id', 'کوهنورد', User::class)->required(),
                Field::relation('destination_id', 'قله', Destination::class)->required()
                    ->hint('فقط قله‌های دارای ارتفاع در نردبان حساب می‌شوند.'),
                Field::date('climbed_on', 'تاریخ صعود'),
                Field::text('note', 'یادداشت')->wide(),
            ],
        ];
    }

    public function rules(?Model $record): array
    {
        $rules = parent::rules($record);
        $rules['destination_id'][] = Rule::unique('peak_ascents', 'destination_id')
            ->where('user_id', request()->input('user_id'))
            ->ignore($record?->getKey());

        return $rules;
    }
}
