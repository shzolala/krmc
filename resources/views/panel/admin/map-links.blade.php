@php
    $selectedTypes = $options['types'] ?? [];
    $selectedIds = $options['ids'] ?? [];
    $inputClass = 'w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900';
@endphp

<x-layouts::admin title="لینک و QR نقشه">
    <x-admin.page-header
        title="ساخت لینک و QR نقشه"
        subtitle="نقشه را روی لایه‌ها، مکان‌ها یا نقطه‌ای مشخص تنظیم کنید و لینک یا QR آن را برای نصب در محل بگیرید. این تنظیمات فقط در آدرس هستند و در صفحه نقشه نمایش داده نمی‌شوند."
        icon="qr-code"
        :back="route('panel.admin.dashboard')"
        back-label="داشبورد"
    />

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <form method="GET" action="{{ route('panel.admin.map-links') }}" class="flex flex-col gap-6">
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <x-admin.card title="لایه‌های قابل نمایش" description="اگر انتخاب نکنید همه لایه‌ها نمایش داده می‌شوند.">
                <div class="flex flex-wrap gap-x-5 gap-y-3">
                    @foreach ($layers as $type => $layer)
                        <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                            <input type="checkbox" name="types[]" value="{{ $type }}" @checked(in_array($type, $selectedTypes, true))>
                            <span class="size-2.5 rounded-full" style="background: {{ $layer['color'] }}"></span>
                            {{ $layer['label'] }}
                        </label>
                    @endforeach
                </div>
            </x-admin.card>

            <x-admin.card title="فقط این مکان‌ها" description="اگر مکانی انتخاب شود، نقشه فقط همان‌ها را نشان می‌دهد.">
                <div class="space-y-2">
                    @foreach ($places as $type => $items)
                        <details class="rounded-lg border border-zinc-200 dark:border-zinc-800" @open(collect($items)->pluck('id')->intersect($selectedIds)->isNotEmpty())>
                            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold">
                                {{ $layers[$type]['label'] }}
                                <span class="text-xs font-normal text-zinc-500">({{ fa_num($items->count()) }})</span>
                            </summary>
                            <div class="grid max-h-60 gap-1.5 overflow-y-auto border-t border-zinc-100 p-3 sm:grid-cols-2 dark:border-zinc-800">
                                @foreach ($items as $item)
                                    <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                                        <input type="checkbox" name="ids[]" value="{{ $item['id'] }}" @checked(in_array($item['id'], $selectedIds, true))>
                                        {{ $item['label'] }}
                                    </label>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </x-admin.card>

            <x-admin.card title="تمرکز روی یک مکان" description="نقشه روی این مکان زوم می‌کند و کارت آن را باز می‌کند (بقیه مکان‌ها پنهان نمی‌شوند).">
                <select name="focus" class="{{ $inputClass }}">
                    <option value="">بدون تمرکز</option>
                    @foreach ($places as $type => $items)
                        <optgroup label="{{ $layers[$type]['label'] }}">
                            @foreach ($items as $item)
                                <option value="{{ $item['id'] }}" @selected(($options['focus'] ?? null) === $item['id'])>{{ $item['label'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </x-admin.card>

            <x-admin.card title="موقعیت مکانی" description="با شعاع، فقط مکان‌های اطراف آن نقطه نمایش داده می‌شود؛ بدون شعاع، نقشه فقط روی نقطه تمرکز می‌کند.">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="text-sm">عرض جغرافیایی
                        <input type="text" inputmode="decimal" dir="ltr" name="lat" value="{{ $options['lat'] ?? '' }}" placeholder="30.2839" class="{{ $inputClass }} mt-1">
                    </label>
                    <label class="text-sm">طول جغرافیایی
                        <input type="text" inputmode="decimal" dir="ltr" name="lng" value="{{ $options['lng'] ?? '' }}" placeholder="57.0834" class="{{ $inputClass }} mt-1">
                    </label>
                    <label class="text-sm">شعاع (کیلومتر)
                        <input type="text" inputmode="decimal" dir="ltr" name="radius" value="{{ $options['radius'] ?? '' }}" placeholder="5" class="{{ $inputClass }} mt-1">
                    </label>
                    <label class="text-sm">زوم (۱ تا ۱۹)
                        <input type="text" inputmode="decimal" dir="ltr" name="zoom" value="{{ $options['zoom'] ?? '' }}" placeholder="14" class="{{ $inputClass }} mt-1">
                    </label>
                </div>
            </x-admin.card>

            <x-admin.card title="موارد دیگر">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm">عبارت جستجو
                        <input type="text" name="q" value="{{ $options['q'] ?? '' }}" class="{{ $inputClass }} mt-1">
                    </label>
                    <label class="text-sm">نوع نقشه
                        <select name="base" class="{{ $inputClass }} mt-1">
                            <option value="">پیش‌فرض (توپوگرافی)</option>
                            <option value="satellite" @selected(($options['base'] ?? null) === 'satellite')>ماهواره</option>
                            <option value="streets" @selected(($options['base'] ?? null) === 'streets')>ساده (راه‌ها و شهرها)</option>
                        </select>
                    </label>
                </div>
            </x-admin.card>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" :href="route('panel.admin.map-links')">پاک کردن</flux:button>
                <flux:button type="submit" variant="primary" icon="qr-code">ساخت لینک و QR</flux:button>
            </div>
        </form>

        <aside class="lg:sticky lg:top-4 lg:self-start">
            <x-admin.card title="نتیجه">
                @if ($url)
                    <div class="mx-auto w-56 rounded-lg bg-white p-2 ring-1 ring-zinc-200 [&>svg]:h-auto [&>svg]:w-full">{!! $qr !!}</div>

                    <label class="mt-4 block text-xs text-zinc-500">لینک کوتاه (روی QR)
                        <input readonly dir="ltr" value="{{ $short->shortUrl() }}" onfocus="this.select()" class="{{ $inputClass }} mt-1 font-mono text-xs">
                    </label>
                    <label class="mt-3 block text-xs text-zinc-500">لینک کامل
                        <textarea readonly dir="ltr" rows="3" onfocus="this.select()" class="{{ $inputClass }} mt-1 font-mono text-xs">{{ $url }}</textarea>
                    </label>
                    <p class="mt-2 text-xs text-zinc-500">تعداد بازدید این لینک: {{ fa_num($short->hits) }}</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <flux:button size="sm" icon="arrow-top-right-on-square" :href="$short->shortUrl()" target="_blank">پیش‌نمایش</flux:button>
                        <flux:button size="sm" icon="arrow-down-tray" :href="route('map.short.qr', [$short->code, 'download' => 1])">دانلود QR</flux:button>
                    </div>
                @else
                    <p class="text-sm text-zinc-500">گزینه‌ها را انتخاب کنید و «ساخت لینک و QR» را بزنید.</p>
                @endif
            </x-admin.card>
        </aside>
    </div>
</x-layouts::admin>
