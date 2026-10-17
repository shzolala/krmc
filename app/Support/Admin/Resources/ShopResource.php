<?php

namespace App\Support\Admin\Resources;

use App\Models\Shop;
use App\Models\User;
use App\Support\Admin\AdminResource;
use App\Support\Admin\Column;
use App\Support\Admin\Field;
use Illuminate\Database\Eloquent\Model;

class ShopResource extends AdminResource
{
    public function model(): string
    {
        return Shop::class;
    }

    public function key(): string
    {
        return 'shops';
    }

    public function singular(): string
    {
        return 'فروشگاه';
    }

    public function plural(): string
    {
        return 'فروشگاه‌ها';
    }

    public function icon(): string
    {
        return 'shopping-bag';
    }

    public function group(): string
    {
        return 'directory';
    }

    protected function publicRoute(): ?string
    {
        return 'shops.show';
    }

    public function searchable(): array
    {
        return ['name', 'city'];
    }

    public function with(): array
    {
        return ['user:id,name'];
    }

    public function secondaryText(Model $record): ?string
    {
        return $record->user?->name ? 'مالک: '.$record->user->name : null;
    }

    public function filters(): array
    {
        return [
            'is_verified' => ['label' => 'تأیید هویت', 'options' => $this->yesNo()],
            'is_featured' => ['label' => 'ویژه', 'options' => $this->yesNo()],
            'is_premium' => ['label' => 'پریمیوم', 'options' => $this->yesNo()],
        ];
    }

    public function columns(): array
    {
        return [
            Column::title('name', 'نام فروشگاه'),
            Column::text('city', 'شهر', true),
            Column::toggle('is_verified', 'تأیید'),
            Column::toggle('is_featured', 'ویژه'),
            Column::toggle('is_premium', 'پریمیوم'),
        ];
    }

    public function fields(): array
    {
        return [
            'اطلاعات اصلی' => [
                Field::text('name', 'نام فروشگاه')->required(),
                $this->slugField(),
                Field::relation('user_id', 'مالک (کاربر)', User::class),
                Field::text('summary', 'خلاصه', 500)->wide(),
                Field::text('logo_path', 'مسیر / نشانی لوگو'),
            ],
            'موقعیت' => [
                Field::text('city', 'شهر')->required(),
                ...$this->geoFields(),
            ],
            'وضعیت' => [
                Field::toggle('is_verified', 'تأیید هویت شده'),
                Field::toggle('is_featured', 'نمایش ویژه'),
                Field::toggle('is_premium', 'عضویت پریمیوم'),
            ],
        ];
    }
}
