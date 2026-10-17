@props([
    'field',
    'record' => null,
])

@php
    $name = $field->name;
    $value = old($name, $field->formValue($record));
    $label = $field->label.($field->isRequired() ? ' *' : '');
    $ltr = in_array($field->type, ['number', 'decimal', 'datetime', 'json'], true)
        || in_array($name, ['slug', 'catalog_id', 'logo_path', 'cover_path', 'avatar_path', 'farakouh_url'], true);
@endphp

<div @class(['md:col-span-2' => $field->isWide()])>
    @switch($field->type)
        @case('toggle')
            <input type="hidden" name="{{ $name }}" value="0">
            <flux:checkbox name="{{ $name }}" value="1" :checked="(bool) $value" :label="$field->label" />
            @break

        @case('textarea')
        @case('json')
        @case('tags')
            <flux:textarea
                name="{{ $name }}"
                :label="$label"
                :description="$field->getHint()"
                rows="{{ $field->type === 'textarea' ? 7 : 4 }}"
                :dir="$field->type === 'json' ? 'ltr' : null"
                class="{{ $field->type === 'json' ? 'font-mono text-xs' : '' }}"
            >{{ is_array($value) ? '' : $value }}</flux:textarea>
            @break

        @case('select')
        @case('relation')
            <flux:select name="{{ $name }}" :label="$label" :description="$field->getHint()">
                <option value="">— انتخاب نشده —</option>
                @foreach ($field->options() as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </flux:select>
            @break

        @case('number')
        @case('decimal')
            <flux:input
                name="{{ $name }}"
                :label="$label"
                :description="$field->getHint()"
                type="number"
                :step="$field->type === 'decimal' ? 'any' : '1'"
                :value="$value"
                dir="ltr"
            />
            @break

        @case('datetime')
            <flux:input name="{{ $name }}" :label="$label" :description="$field->getHint()" type="datetime-local" :value="$value" dir="ltr" />
            @break

        @default
            <flux:input
                name="{{ $name }}"
                :label="$label"
                :description="$field->getHint()"
                :value="$value"
                :dir="$ltr ? 'ltr' : null"
            />
    @endswitch
</div>
