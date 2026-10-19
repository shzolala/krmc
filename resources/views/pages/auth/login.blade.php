<x-layouts::auth title="ورود">
    <div class="flex flex-col gap-6">
        <x-auth-header title="ورود به حساب کاربری" description="برای دسترسی به پنل، شماره موبایل و رمز عبور خود را وارد نمایید." />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="mobile"
                label="شماره موبایل"
                :value="old('mobile')"
                type="tel"
                required
                autofocus
                autocomplete="tel"
                placeholder="0912xxxxxxx"
                dir="ltr"
            />

            <flux:input
                name="password"
                label="رمز عبور"
                type="password"
                required
                autocomplete="current-password"
                placeholder="رمز عبور"
                viewable
            />

            <flux:checkbox name="remember" label="مرا به خاطر بسپار" :checked="old('remember')" />

            <flux:button variant="primary" type="submit" class="w-full !bg-navy-900 !text-white hover:!bg-navy-700" data-test="login-button">
                ورود
            </flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-muted rtl:space-x-reverse">
            <span>هنوز عضو نیستید؟</span>
            <a href="{{ route('register') }}" class="font-bold text-navy-900 underline decoration-brand decoration-2 underline-offset-4 hover:text-navy-700" wire:navigate>ساخت حساب</a>
        </div>
    </div>
</x-layouts::auth>
