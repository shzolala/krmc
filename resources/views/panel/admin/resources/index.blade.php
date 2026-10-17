@php
    $key = $definition->key();
    $hasFilters = $search !== '' || count($activeFilters) > 0;
    $sortUrl = function (string $column) use ($sort, $direction): string {
        $next = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';

        return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $next, 'page' => null]);
    };
@endphp

<x-layouts::admin :title="$definition->plural()">
    <x-admin.page-header
        :title="$definition->plural()"
        :subtitle="fa_num($total).' مورد ثبت‌شده'"
        :icon="$definition->icon()"
    >
        <flux:button variant="primary" icon="plus" :href="route('panel.admin.resources.create', $key)" wire:navigate>
            افزودن {{ $definition->singular() }}
        </flux:button>
    </x-admin.page-header>

    <x-admin.card :padded="false">
        <form method="GET" action="{{ route('panel.admin.resources.index', $key) }}" class="flex flex-wrap items-end gap-3 border-b border-zinc-100 p-4 dark:border-zinc-800">
            <div class="min-w-56 flex-1">
                <flux:input name="q" :value="$search" icon="magnifying-glass" placeholder="جستجو در {{ $definition->plural() }}…" />
            </div>
            @foreach ($definition->filters() as $name => $filter)
                <div class="w-40">
                    <flux:select name="{{ $name }}" onchange="this.form.submit()">
                        <option value="">{{ $filter['label'] }}: همه</option>
                        @foreach ($filter['options'] as $value => $label)
                            <option value="{{ $value }}" @selected(($activeFilters[$name] ?? null) === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </flux:select>
                </div>
            @endforeach
            @if ($sort)
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="dir" value="{{ $direction }}">
            @endif
            <flux:button type="submit" icon="funnel">اعمال</flux:button>
            @if ($hasFilters || $sort)
                <flux:button variant="ghost" :href="route('panel.admin.resources.index', $key)" wire:navigate>پاک‌کردن</flux:button>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/50">
                    <tr>
                        @foreach ($definition->columns() as $column)
                            <th class="whitespace-nowrap px-4 py-3 text-start font-semibold">
                                @if ($column->sortable)
                                    <a href="{{ $sortUrl($column->name) }}" class="inline-flex items-center gap-1 hover:text-zinc-900 dark:hover:text-zinc-100">
                                        {{ $column->label }}
                                        @if ($sort === $column->name)
                                            @if ($direction === 'asc') <flux:icon.arrow-up class="size-3" /> @else <flux:icon.arrow-down class="size-3" /> @endif
                                        @endif
                                    </a>
                                @else
                                    {{ $column->label }}
                                @endif
                            </th>
                        @endforeach
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($records as $record)
                        @php
                            $publicUrl = $definition->publicUrl($record);
                            $recordTitle = $definition->recordTitle($record);
                        @endphp
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                            @foreach ($definition->columns() as $column)
                                @php $value = data_get($record, $column->name); @endphp
                                <td class="px-4 py-3 align-middle">
                                    @switch($column->type)
                                        @case('title')
                                            <a href="{{ route('panel.admin.resources.edit', ['resource' => $key, 'id' => $record->id]) }}" wire:navigate class="font-semibold text-zinc-900 hover:text-brand dark:text-zinc-100">
                                                {{ $recordTitle }}
                                            </a>
                                            @if ($secondary = $definition->secondaryText($record))
                                                <div class="text-xs text-zinc-500">{{ $secondary }}</div>
                                            @endif
                                            @break

                                        @case('badge')
                                            @if ($value instanceof \BackedEnum)
                                                <flux:badge size="sm">{{ method_exists($value, 'label') ? $value->label() : $value->value }}</flux:badge>
                                            @else
                                                <span class="text-zinc-400">—</span>
                                            @endif
                                            @break

                                        @case('toggle')
                                            <form method="POST" action="{{ route('panel.admin.resources.toggle', ['resource' => $key, 'id' => $record->id, 'field' => $column->name]) }}">
                                                @csrf
                                                <button type="submit" title="تغییر وضعیت «{{ $column->label }}»"
                                                    @class([
                                                        'inline-flex size-7 items-center justify-center rounded-full transition',
                                                        'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300' => $value,
                                                        'bg-zinc-100 text-zinc-400 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700' => ! $value,
                                                    ])>
                                                    @if ($value) <flux:icon.check class="size-4" /> @else <flux:icon.x-mark class="size-4" /> @endif
                                                </button>
                                            </form>
                                            @break

                                        @case('number')
                                            <span class="tabular-nums">{{ $value === null ? '—' : fa_num($value) }}</span>
                                            @break

                                        @case('date')
                                            <span class="whitespace-nowrap text-xs">{{ fa_date($value) }}</span>
                                            @break

                                        @default
                                            @if (is_numeric($value))
                                                <span class="tabular-nums">{{ fa_num($value) }}</span>
                                            @else
                                                {{ $value ?: '—' }}
                                            @endif
                                    @endswitch
                                </td>
                            @endforeach
                            <td class="whitespace-nowrap px-4 py-3 text-end">
                                <div class="inline-flex items-center gap-1">
                                    @if ($publicUrl)
                                        <flux:button size="xs" variant="ghost" icon="arrow-top-right-on-square" :href="$publicUrl" target="_blank" title="مشاهده در سایت" />
                                    @endif
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" :href="route('panel.admin.resources.edit', ['resource' => $key, 'id' => $record->id])" wire:navigate title="ویرایش" />
                                    <form method="POST" action="{{ route('panel.admin.resources.destroy', ['resource' => $key, 'id' => $record->id]) }}"
                                        onsubmit="return confirm('{{ $definition->singular() }} «{{ addslashes($recordTitle) }}» حذف شود؟ این کار قابل بازگشت نیست.')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button size="xs" variant="ghost" type="submit" icon="trash" class="text-red-600!" title="حذف" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($definition->columns()) + 1 }}" class="px-4 py-16 text-center">
                                <flux:icon :name="$definition->icon()" class="mx-auto size-10 text-zinc-300" />
                                <p class="mt-3 text-sm text-zinc-500">
                                    {{ $hasFilters ? 'موردی با این جستجو یا فیلتر پیدا نشد.' : 'هنوز '.$definition->singular().' ثبت نشده است.' }}
                                </p>
                                @unless ($hasFilters)
                                    <flux:button class="mt-4" variant="primary" icon="plus" :href="route('panel.admin.resources.create', $key)" wire:navigate>
                                        افزودن {{ $definition->singular() }}
                                    </flux:button>
                                @endunless
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="border-t border-zinc-100 p-4 dark:border-zinc-800">{{ $records->links() }}</div>
        @endif
    </x-admin.card>
</x-layouts::admin>
