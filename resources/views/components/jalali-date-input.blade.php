@props([
    'name',
    'label' => null,
    'description' => null,
    'value' => null,
    'withTime' => false,
    'group' => null,
    'role' => null,
])

@php $id = 'jd-'.str_replace(['[', ']', '.'], '-', $name); @endphp

<flux:field>
    @if ($label)
        <flux:label for="{{ $id }}">{{ $label }}</flux:label>
    @endif
    @if ($description)
        <flux:description>{{ $description }}</flux:description>
    @endif

    <div
        x-data="jalaliPicker({ value: @js($value ?: ''), withTime: @js($withTime), group: @js($group), role: @js($role) })"
        x-on:keydown.escape.stop="open = false"
        x-on:click.outside="open = false"
        class="relative"
    >
        <input type="hidden" name="{{ $name }}" :value="iso">

        <div class="relative">
            <input
                id="{{ $id }}" type="text" x-model="text" dir="ltr" autocomplete="off" inputmode="numeric"
                placeholder="۱۴۰۵/۰۱/۰۱" aria-haspopup="dialog" :aria-expanded="open"
                x-on:blur="parseTyped()" x-on:keydown.enter.prevent="parseTyped(); open = false" x-on:keydown.arrow-down.prevent="open = true; focusView()"
                class="h-10 w-full rounded-lg border border-zinc-200 bg-white pl-11 pr-3 text-left text-sm shadow-xs focus:outline-2 focus:outline-offset-1 focus:outline-brand dark:border-zinc-700 dark:bg-zinc-900"
            >
            <button type="button" x-on:click="toggle()" aria-label="باز کردن تقویم" class="absolute left-1 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded-md text-zinc-500 hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:hover:bg-zinc-800">
                <flux:icon.calendar-days class="size-5" />
            </button>
        </div>

        <div x-show="open" x-transition.opacity style="display: none" role="dialog" aria-label="انتخاب تاریخ شمسی"
            class="absolute start-0 z-30 mt-1 w-72 rounded-xl border border-zinc-200 bg-white p-3 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-2 flex items-center justify-between">
                <button type="button" x-on:click="prev()" aria-label="قبلی" class="flex size-8 items-center justify-center rounded-md hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:hover:bg-zinc-800">
                    <flux:icon.chevron-right class="size-4" />
                </button>
                <button type="button" x-on:click="zoomOut()" :disabled="mode === 'years'" aria-live="polite" aria-label="تغییر ماه یا سال"
                    class="flex items-center gap-1 rounded-md px-2 py-1 text-sm font-bold hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand disabled:cursor-default disabled:hover:bg-transparent dark:hover:bg-zinc-800">
                    <span x-text="title"></span>
                    <flux:icon.chevron-down class="size-3.5 text-zinc-400" x-show="mode !== 'years'" />
                </button>
                <button type="button" x-on:click="next()" aria-label="بعدی" class="flex size-8 items-center justify-center rounded-md hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:hover:bg-zinc-800">
                    <flux:icon.chevron-left class="size-4" />
                </button>
            </div>

            <div x-show="mode === 'days'">
                <div class="grid grid-cols-7 text-center text-xs text-zinc-500" aria-hidden="true">
                    <template x-for="name in weekdays" :key="name"><span class="py-1" x-text="name"></span></template>
                </div>

                <div class="grid grid-cols-7 gap-y-0.5 text-center text-sm">
                    <template x-for="(cell, index) in cells" :key="index">
                        <div :class="{ 'bg-brand/15': cell && cell.inRange }">
                            <button type="button" x-show="cell" x-on:click="pick(cell.day)" :aria-label="cell.aria" :aria-pressed="cell.selected" :disabled="cell.disabled"
                                :class="{
                                    'bg-navy-900 text-white hover:bg-navy-800': cell.selected,
                                    'ring-1 ring-brand': (cell.today || cell.peer) && ! cell.selected,
                                    'text-red-600': cell.friday && ! cell.selected && ! cell.disabled,
                                    'text-zinc-300 line-through dark:text-zinc-600': cell.disabled,
                                    'hover:bg-zinc-100 dark:hover:bg-zinc-800': ! cell.selected && ! cell.disabled,
                                }"
                                class="size-9 rounded-lg tabular-nums focus-visible:outline-2 focus-visible:outline-brand">
                                <span x-text="cell.label"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="mode === 'months'" class="grid grid-cols-3 gap-1.5 py-1">
                <template x-for="item in monthCells" :key="item.month">
                    <button type="button" x-on:click="pickMonth(item.month)"
                        :class="{
                            'bg-navy-900 text-white hover:bg-navy-800': item.selected,
                            'ring-1 ring-brand': item.today && ! item.selected,
                            'hover:bg-zinc-100 dark:hover:bg-zinc-800': ! item.selected,
                        }"
                        class="h-11 rounded-lg text-sm focus-visible:outline-2 focus-visible:outline-brand" x-text="item.name"></button>
                </template>
            </div>

            <div x-show="mode === 'years'" class="grid grid-cols-3 gap-1.5 py-1">
                <template x-for="item in years" :key="item.year">
                    <button type="button" x-on:click="pickYear(item.year)"
                        :class="{
                            'bg-navy-900 text-white hover:bg-navy-800': item.selected,
                            'ring-1 ring-brand': item.today && ! item.selected,
                            'text-zinc-400': item.muted && ! item.selected,
                            'hover:bg-zinc-100 dark:hover:bg-zinc-800': ! item.selected,
                        }"
                        class="h-11 rounded-lg text-sm tabular-nums focus-visible:outline-2 focus-visible:outline-brand" x-text="item.label"></button>
                </template>
            </div>

            <template x-if="withTime">
                <div class="mt-3 flex items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                    <label for="{{ $id }}-time" class="text-xs text-zinc-500">ساعت</label>
                    <input id="{{ $id }}-time" type="time" x-model="time" x-on:change="updateTime()" dir="ltr"
                        class="h-8 rounded-md border border-zinc-200 bg-white px-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                </div>
            </template>

            <div class="mt-3 flex items-center justify-between border-t border-zinc-100 pt-3 text-xs dark:border-zinc-800">
                <button type="button" x-on:click="pickToday()" class="rounded-md px-2 py-1 font-medium text-navy-700 hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:text-brand dark:hover:bg-zinc-800">امروز</button>
                <button type="button" x-on:click="clear()" class="rounded-md px-2 py-1 text-zinc-500 hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:hover:bg-zinc-800">پاک کردن</button>
            </div>
        </div>
    </div>

    <flux:error name="{{ $name }}" />
</flux:field>
