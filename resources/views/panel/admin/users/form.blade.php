@php
    $isEdit = $user->exists;
    $heading = $isEdit ? 'ویرایش کاربر' : 'کاربر جدید';
    $isSelf = $isEdit && $user->is(auth()->user());
@endphp

<x-layouts::admin :title="$heading">
    <x-admin.page-header
        :title="$heading"
        :subtitle="$isEdit ? $user->name.' — عضو از '.fa_date($user->created_at, false, true) : 'ایجاد حساب جدید و تعیین سطح دسترسی'"
        icon="users"
        :back="route('panel.admin.users.index')"
        back-label="کاربران"
    >
        @if ($isEdit)
            <flux:button icon="arrow-top-right-on-square" :href="route('profiles.show', $user)" target="_blank">پروفایل عمومی</flux:button>
            @if (! $user->isAdmin() && ! $isSelf)
                <form method="POST" action="{{ route('panel.admin.users.impersonate', $user) }}">
                    @csrf
                    <flux:button type="submit" icon="eye">ورود به‌جای کاربر</flux:button>
                </form>
            @endif
        @endif
    </x-admin.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ $isEdit ? route('panel.admin.users.update', $user) : route('panel.admin.users.store') }}" class="flex flex-col gap-6 lg:col-span-2">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <x-admin.card title="اطلاعات حساب">
                <div class="grid gap-5 md:grid-cols-2">
                    <flux:input name="name" label="نام *" :value="old('name', $user->name)" required />
                    <flux:input name="username" label="یوزرنیم *" :value="old('username', $user->username)" required dir="ltr" />
                    <flux:input name="mobile" label="موبایل *" type="tel" :value="old('mobile', $user->mobile)" required dir="ltr" />
                    <flux:input name="email" label="ایمیل" type="email" :value="old('email', $user->email)" dir="ltr" />
                </div>
            </x-admin.card>

            <x-admin.card title="نقش و دسترسی">
                <div class="grid gap-5 md:grid-cols-2">
                    <flux:select name="role" label="نقش / سطح دسترسی *" :disabled="$isSelf">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </flux:select>
                    <flux:select name="primary_club_id" label="باشگاه اصلی">
                        <option value="">— بدون باشگاه —</option>
                        @foreach ($clubs as $id => $name)
                            <option value="{{ $id }}" @selected((string) old('primary_club_id', $user->primary_club_id) === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                @if ($isSelf)
                    <input type="hidden" name="role" value="{{ $user->role->value }}">
                    <p class="mt-3 text-xs text-zinc-500">نقش حساب خودتان قابل تغییر نیست.</p>
                @else
                    <p class="mt-3 text-xs text-zinc-500">نقش‌ها و جزئیات دسترسی هر کدام را در صفحه «نقش‌ها و دسترسی‌ها» ببینید.</p>
                @endif
            </x-admin.card>

            <x-admin.card :title="$isEdit ? 'تغییر رمز عبور' : 'رمز عبور'">
                <flux:input name="password" type="password" :label="$isEdit ? 'رمز عبور جدید' : 'رمز عبور *'" :description="$isEdit ? 'برای حفظ رمز فعلی خالی بگذارید.' : null" :required="! $isEdit" viewable dir="ltr" autocomplete="new-password" />
            </x-admin.card>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" :href="route('panel.admin.users.index')" wire:navigate>انصراف</flux:button>
                <flux:button type="submit" variant="primary" icon="check">{{ $isEdit ? 'ذخیره تغییرات' : 'ایجاد کاربر' }}</flux:button>
            </div>
        </form>

        <div class="flex flex-col gap-6">
            @if ($isEdit)
                <x-admin.card title="موجودیت‌های مرتبط" :padded="false">
                    <ul class="divide-y divide-zinc-100 text-sm dark:divide-zinc-800">
                        @forelse ($user->ownedClubs as $club)
                            <li><a class="flex items-center gap-2 px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50" href="{{ route('panel.admin.resources.edit', ['resource' => 'clubs', 'id' => $club->id]) }}" wire:navigate>
                                <flux:icon.building-office-2 class="size-4 text-zinc-400" /> باشگاه: {{ $club->name }}</a></li>
                        @empty
                        @endforelse
                        @if ($user->coachProfile)
                            <li><a class="flex items-center gap-2 px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50" href="{{ route('panel.admin.resources.edit', ['resource' => 'coaches', 'id' => $user->coachProfile->id]) }}" wire:navigate>
                                <flux:icon.academic-cap class="size-4 text-zinc-400" /> پروفایل {{ $user->coachProfile->roleLabel() }}</a></li>
                        @endif
                        @if ($user->shop)
                            <li><a class="flex items-center gap-2 px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50" href="{{ route('panel.admin.resources.edit', ['resource' => 'shops', 'id' => $user->shop->id]) }}" wire:navigate>
                                <flux:icon.shopping-bag class="size-4 text-zinc-400" /> فروشگاه: {{ $user->shop->name }}</a></li>
                        @endif
                        @if ($user->ownedClubs->isEmpty() && ! $user->coachProfile && ! $user->shop)
                            <li class="px-5 py-5 text-center text-xs text-zinc-500">این کاربر هنوز پروفایل یا کسب‌وکاری ندارد.</li>
                        @endif
                    </ul>
                </x-admin.card>

                @unless ($isSelf)
                    <x-admin.card title="منطقه خطر" class="border-red-200 dark:border-red-900/50">
                        <form method="POST" action="{{ route('panel.admin.users.destroy', $user) }}" onsubmit="return confirm('این کاربر و نظرات او حذف شود؟')" class="space-y-3">
                            @csrf
                            @method('DELETE')
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">حذف حساب کاربر و تمام نظرات او غیرقابل بازگشت است.</p>
                            <flux:button type="submit" variant="danger" icon="trash" class="w-full">حذف کاربر</flux:button>
                        </form>
                    </x-admin.card>
                @endunless
            @else
                <x-admin.card title="راهنما">
                    <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                        پس از ایجاد کاربر می‌توانید برای نقش‌های باشگاه، مربی، سرپرست و فروشگاه، پروفایل مربوطه را از بخش دایرکتوری ایجاد و به این کاربر متصل کنید.
                    </p>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts::admin>
