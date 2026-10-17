@if (session()->has(\App\Http\Controllers\Panel\Admin\ImpersonationController::SESSION_KEY) && auth()->check())
    <div class="sticky top-0 z-50 flex flex-wrap items-center justify-center gap-3 bg-amber-400 px-4 py-2 text-sm font-semibold text-amber-950">
        <flux:icon.eye class="size-4" />
        <span>شما اکنون به‌عنوان «{{ auth()->user()->name }}» ({{ auth()->user()->roleLabel() }}) وارد شده‌اید.</span>
        <x-admin.role-switcher compact />
        <form method="POST" action="{{ route('panel.impersonation.stop') }}">
            @csrf
            <button type="submit" class="rounded-md bg-amber-950 px-3 py-1 text-xs text-amber-50 hover:bg-amber-900">
                بازگشت به حساب مدیر
            </button>
        </form>
    </div>
@endif
