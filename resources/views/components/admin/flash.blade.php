@if (session('status'))
    <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200" role="status">
        <flux:icon.check-circle class="mt-0.5 size-5 shrink-0" />
        <span>{{ session('status') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200" role="alert">
        <flux:icon.exclamation-triangle class="mt-0.5 size-5 shrink-0" />
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any() && ! request()->routeIs('panel.admin.reviews'))
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-200" role="alert">
        <div class="mb-1 flex items-center gap-2 font-semibold">
            <flux:icon.exclamation-triangle class="size-5" />
            ذخیره انجام نشد؛ لطفاً موارد زیر را بررسی کنید:
        </div>
        <ul class="list-disc space-y-0.5 pe-5 ps-0 text-red-800 dark:text-red-300">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
