<?php

namespace App\Support\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class AdminResource
{
    /**
     * @return class-string<Model>
     */
    abstract public function model(): string;

    abstract public function key(): string;

    abstract public function singular(): string;

    abstract public function plural(): string;

    abstract public function icon(): string;

    abstract public function group(): string;

    /**
     * @return list<Column>
     */
    abstract public function columns(): array;

    /**
     * Form fields grouped by section title.
     *
     * @return array<string, list<Field>>
     */
    abstract public function fields(): array;

    /**
     * @return list<string>
     */
    public function searchable(): array
    {
        return ['name'];
    }

    /**
     * @return array<string, array{label: string, options: array<string, string>}>
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public function with(): array
    {
        return [];
    }

    public function titleAttribute(): string
    {
        return 'name';
    }

    public function publicUrl(Model $record): ?string
    {
        $route = $this->publicRoute();

        return $route !== null && $record->getAttribute('slug')
            ? route($route, $record)
            : null;
    }

    protected function publicRoute(): ?string
    {
        return null;
    }

    public function recordTitle(Model $record): string
    {
        return (string) $record->getAttribute($this->titleAttribute());
    }

    public function secondaryText(Model $record): ?string
    {
        return null;
    }

    /**
     * @return list<Field>
     */
    public function flatFields(): array
    {
        return array_merge(...array_values($this->fields()));
    }

    public function table(): string
    {
        return (new ($this->model()))->getTable();
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(?Model $record): array
    {
        $rules = [];

        foreach ($this->flatFields() as $field) {
            $rules[$field->name] = $field->validationRules($this->table(), $record);
        }

        return $rules;
    }

    /**
     * Casts validated input and fills generated values (slug, catalog ids).
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    public function prepare(array $validated, ?Model $record): array
    {
        $data = [];

        foreach ($this->flatFields() as $field) {
            $data[$field->name] = $field->castInput($validated[$field->name] ?? null);
        }

        foreach ($this->flatFields() as $field) {
            if (! $field->hasAutoValue() || filled($data[$field->name])) {
                continue;
            }

            $existing = $record?->getAttribute($field->name);
            $data[$field->name] = filled($existing) ? $existing : $field->generate($data);
        }

        return $data;
    }

    /**
     * @return list<Field>
     */
    protected function geoFields(): array
    {
        return [
            Field::decimal('latitude', 'عرض جغرافیایی')->rules(['between:-90,90']),
            Field::decimal('longitude', 'طول جغرافیایی')->rules(['between:-180,180']),
            Field::text('address', 'نشانی'),
        ];
    }

    protected function slugField(string $source = 'name'): Field
    {
        $table = $this->table();

        return Field::text('slug', 'نامک (اسلاگ)')
            ->unique()
            ->hint('در صورت خالی بودن، به‌صورت خودکار از عنوان ساخته می‌شود.')
            ->autoValue(function (array $data) use ($table, $source): string {
                $base = Str::slug((string) ($data[$source] ?? '')) ?: 'item';
                $slug = $base;
                $counter = 2;

                while (DB::table($table)->where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$counter++;
                }

                return $slug;
            });
    }

    protected function catalogIdField(string $prefix): Field
    {
        return Field::text('catalog_id', 'شناسه کاتالوگ')
            ->unique()
            ->hint('در صورت خالی بودن، خودکار ساخته می‌شود.')
            ->autoValue(fn (): string => $prefix.'-'.Str::lower(Str::random(8)));
    }

    /**
     * @return array<string, string>
     */
    protected function yesNo(): array
    {
        return ['1' => 'بله', '0' => 'خیر'];
    }

    /**
     * @param  class-string<\BackedEnum>  $enum
     * @return array<string, string>
     */
    protected function enumOptions(string $enum): array
    {
        $options = [];

        foreach ($enum::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
