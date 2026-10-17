@props(['compact' => false])

@php
    use App\Http\Controllers\Panel\Admin\ImpersonationController;
    use App\Support\PanelRegistry;

    $isImpersonating = session()->has(ImpersonationController::SESSION_KEY);
    $visible = PanelRegistry::switcherEnabled() && auth()->check() && ($compact ? $isImpersonating : auth()->user()->isAdmin() && ! $isImpersonating);
    $accounts = $visible ? PanelRegistry::demoAccounts() : collect();
@endphp

@if ($accounts->isNotEmpty())
    <form method="POST" action="{{ route('panel.impersonation.switch') }}" class="relative flex items-center">
        @csrf
        <flux:icon.eye class="pointer-events-none absolute start-3 size-4 text-zinc-400" />
        <select name="mobile" onchange="this.form.submit()" aria-label="تغییر نقش نمایشی" title="مشاهده پنل نقش‌ها با کاربران نمونه"
            class="h-9 cursor-pointer appearance-none rounded-lg border border-zinc-200 bg-white ps-9 pe-3 text-sm font-medium text-zinc-700 shadow-xs hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 {{ $compact ? 'text-amber-950' : '' }}">
            <option value="" disabled @selected(! $isImpersonating)>مشاهده به‌عنوان…</option>
            @foreach ($accounts as $account)
                <option value="{{ $account['mobile'] }}" @selected(auth()->user()->mobile === $account['mobile'])>
                    {{ $account['label'] }} — {{ $account['name'] }}
                </option>
            @endforeach
        </select>
    </form>
@endif
