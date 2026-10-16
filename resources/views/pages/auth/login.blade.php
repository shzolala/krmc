<x-layouts::auth title="ورود">
    <div class="flex flex-col gap-6">
        <x-auth-header title="ورود به پنل" description="با شماره موبایل و رمز عبور وارد شوید. به‌زودی ورود با OTP پیامکی هم فعال می‌شود." />

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

            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                ورود
            </flux:button>
        </form>

        @if (($demoAccounts ?? collect())->isNotEmpty())
            <div class="space-y-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                <flux:heading size="sm">ورود سریع تست</flux:heading>
                <flux:text class="text-sm">رمز همه اکانت‌ها: <span dir="ltr" class="font-mono">password</span></flux:text>
                <form method="POST" action="{{ route('dev.switch-demo-account') }}">
                    @csrf
                    <flux:select name="mobile" label="انتخاب نقش / پنل" onchange="this.form.submit()">
                        <option value="" selected disabled>انتخاب کنید…</option>
                        @foreach ($demoAccounts as $account)
                            <option value="{{ $account['mobile'] }}">
                                {{ $account['label'] }} — {{ $account['mobile'] }} ({{ '@'.$account['username'] }})
                            </option>
                        @endforeach
                    </flux:select>
                </form>
            </div>
        @endif

        <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
            <span>حساب ندارید؟</span>
            <flux:link :href="route('register')" wire:navigate>ثبت‌نام</flux:link>
        </div>
    </div>
</x-layouts::auth>
