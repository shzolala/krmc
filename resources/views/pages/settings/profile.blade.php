<?php

use App\Concerns\ProfileValidationRules;
use App\Support\MobileNumber;
use App\Support\Username;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('تنظیمات پروفایل')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';

    public string $mobile = '';

    public string $username = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->mobile = $user->mobile;
        $this->username = $user->username;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $this->mobile = MobileNumber::normalize($this->mobile) ?? '';
        $this->username = Username::normalize($this->username) ?? '';

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);
        $user->save();

        Flux::toast(variant: 'success', text: 'پروفایل به‌روزرسانی شد.');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout heading="پروفایل" subheading="نام، موبایل و یوزرنیم عمومی را مدیریت کنید">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" label="نام" type="text" required autofocus autocomplete="name" />

            <flux:input
                wire:model="mobile"
                label="شماره موبایل"
                type="tel"
                required
                autocomplete="tel"
                dir="ltr"
                description="شناسه یکتای ورود؛ بعداً برای OTP پیامکی هم استفاده می‌شود"
            />

            <flux:input
                wire:model="username"
                label="یوزرنیم"
                type="text"
                required
                autocomplete="username"
                dir="ltr"
                description="آدرس پروفایل عمومی: /u/{{ auth()->user()->username }}"
            />

            <div class="flex flex-wrap items-center justify-between gap-3">
                <flux:button
                    :href="route('profiles.show', auth()->user()->username)"
                    variant="ghost"
                    wire:navigate
                >
                    مشاهده پروفایل عمومی
                </flux:button>

                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    ذخیره
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
