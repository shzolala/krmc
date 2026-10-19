@props([
    'field',
    'record' => null,
])

@php
    $name = $field->name;
    $value = old($name, $record === null && $field->type === 'relation' ? request()->query($name, $field->formValue($record)) : $field->formValue($record));
    $label = $field->label.($field->isRequired() ? ' *' : '');
    $ltr = in_array($field->type, ['number', 'decimal', 'json'], true)
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

        @case('image')
            @php
                $stored = $record?->getAttribute($name);
                $previewUrl = match (true) {
                    blank($stored) => null,
                    str_starts_with($stored, 'http'), str_starts_with($stored, '/') => $stored,
                    str_starts_with($stored, 'images/') => asset($stored),
                    default => \Illuminate\Support\Facades\Storage::disk('public')->url($stored),
                };
            @endphp
            <flux:field>
                <flux:label>{{ $label }}</flux:label>
                @if ($previewUrl)
                    <img src="{{ $previewUrl }}" alt="" class="mb-2 aspect-[16/6] w-full max-w-xl rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
                @endif
                <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp"
                    class="block w-full cursor-pointer rounded-lg border border-zinc-200 bg-white text-sm text-zinc-700 file:me-3 file:cursor-pointer file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:file:bg-zinc-800">
                @if ($field->getHint())
                    <flux:description>{{ $field->getHint() }}</flux:description>
                @endif
                <flux:error name="{{ $name }}" />
            </flux:field>
            @break

        @case('date')
        @case('datetime')
            <x-jalali-date-input :name="$name" :label="$label" :description="$field->getHint()" :value="$value" :with-time="$field->type === 'datetime'" :group="$field->getRangeGroup()" :role="$field->getRangeRole()" />
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
