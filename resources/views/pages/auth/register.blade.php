<x-layouts::auth title="ثبت‌نام">
    <div class="flex flex-col gap-6">
        <x-auth-header title="ساخت حساب جدید" description="شماره موبایل شناسه یکتای ورود است؛ یوزرنیم برای پروفایل عمومی استفاده می‌شود." />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="name"
                label="نام"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="نام کامل"
            />

            <flux:input
                name="mobile"
                label="شماره موبایل"
                :value="old('mobile')"
                type="tel"
                required
                autocomplete="tel"
                placeholder="0912xxxxxxx"
                dir="ltr"
            />

            <flux:input
                name="username"
                label="یوزرنیم"
                :value="old('username')"
                type="text"
                required
                autocomplete="username"
                placeholder="username"
                dir="ltr"
                description="فقط حروف انگلیسی، عدد، نقطه، خط زیر و خط تیره"
            />

            <flux:input
                name="password"
                label="رمز عبور"
                type="password"
                required
                autocomplete="new-password"
                placeholder="رمز عبور"
                viewable
            />

            <flux:input
                name="password_confirmation"
                label="تکرار رمز عبور"
                type="password"
                required
                autocomplete="new-password"
                placeholder="تکرار رمز عبور"
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                ساخت حساب
            </flux:button>
        </form>

        <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
            <span>قبلاً حساب دارید؟</span>
            <flux:link :href="route('login')" wire:navigate>ورود</flux:link>
        </div>
    </div>
</x-layouts::auth>
