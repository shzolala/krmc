<x-layouts::app title="ویرایش کاربر">
    <div class="mx-auto flex max-w-xl flex-col gap-6">
        <div>
            <flux:heading size="xl">ویرایش کاربر</flux:heading>
            <flux:subheading>{{ $user->name }}</flux:subheading>
        </div>

        <flux:card>
            <form method="POST" action="{{ route('panel.admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <flux:input name="name" label="نام" :value="old('name', $user->name)" required />
                <flux:input name="mobile" label="موبایل" type="tel" :value="old('mobile', $user->mobile)" required dir="ltr" />
                <flux:input name="username" label="یوزرنیم" :value="old('username', $user->username)" required dir="ltr" />

                <flux:select name="role" label="نقش / سطح دسترسی">
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $user->role->value) === $role->value)>
                            {{ $role->label() }} ({{ $role->value }})
                        </option>
                    @endforeach
                </flux:select>

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary">ذخیره</flux:button>
                    <flux:button :href="route('panel.admin.users.index')" variant="ghost" wire:navigate>انصراف</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
