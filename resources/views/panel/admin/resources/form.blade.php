@php
    $key = $definition->key();
    $isEdit = $record !== null;
    $heading = $isEdit ? 'ویرایش '.$definition->singular() : 'افزودن '.$definition->singular();
    $publicUrl = $isEdit ? $definition->publicUrl($record) : null;
@endphp

<x-layouts::admin :title="$heading">
    <x-admin.page-header
        :title="$heading"
        :subtitle="$isEdit ? $definition->recordTitle($record) : null"
        :icon="$definition->icon()"
        :back="route('panel.admin.resources.index', $key)"
        :back-label="$definition->plural()"
    >
        @if ($publicUrl)
            <flux:button icon="arrow-top-right-on-square" :href="$publicUrl" target="_blank">مشاهده در سایت</flux:button>
        @endif
    </x-admin.page-header>

    <form method="POST"
        action="{{ $isEdit ? route('panel.admin.resources.update', ['resource' => $key, 'id' => $record->id]) : route('panel.admin.resources.store', $key) }}"
        enctype="multipart/form-data"
        class="flex flex-col gap-6">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        @foreach ($definition->fields() as $section => $fields)
            <x-admin.card :title="$section">
                <div class="grid gap-x-5 gap-y-5 md:grid-cols-2">
                    @foreach ($fields as $field)
                        <x-admin.field :field="$field" :record="$record" />
                    @endforeach
                </div>
            </x-admin.card>
        @endforeach

        <div class="sticky bottom-0 z-10 -mx-4 flex items-center justify-between gap-3 border-t border-zinc-200 bg-white/90 px-4 py-3 backdrop-blur sm:mx-0 sm:rounded-xl sm:border dark:border-zinc-800 dark:bg-zinc-900/90">
            <span class="hidden text-xs text-zinc-500 sm:block">
                @if ($isEdit)
                    آخرین ویرایش: {{ fa_date($record->updated_at, true) }}
                @else
                    فیلدهای ستاره‌دار الزامی هستند.
                @endif
            </span>
            <div class="flex gap-2">
                <flux:button variant="ghost" :href="route('panel.admin.resources.index', $key)" wire:navigate>انصراف</flux:button>
                <flux:button type="submit" variant="primary" icon="check">{{ $isEdit ? 'ذخیره تغییرات' : 'ایجاد' }}</flux:button>
            </div>
        </div>
    </form>

    @if ($isEdit)
        <x-admin.card title="منطقه خطر" class="border-red-200 dark:border-red-900/50">
            <form method="POST" action="{{ route('panel.admin.resources.destroy', ['resource' => $key, 'id' => $record->id]) }}"
                onsubmit="return confirm('{{ $definition->singular() }} حذف شود؟ این کار قابل بازگشت نیست.')"
                class="flex flex-wrap items-center justify-between gap-3">
                @csrf
                @method('DELETE')
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    حذف این {{ $definition->singular() }} همراه با نظرات و داده‌های وابسته به آن انجام می‌شود.
                </p>
                <flux:button type="submit" variant="danger" icon="trash">حذف {{ $definition->singular() }}</flux:button>
            </form>
        </x-admin.card>
    @endif
</x-layouts::admin>
