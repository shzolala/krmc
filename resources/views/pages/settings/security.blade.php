<?php

use App\Concerns\PasswordValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('امنیت')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $exception) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $exception;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: 'رمز عبور به‌روزرسانی شد.');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout heading="امنیت" subheading="رمز عبور حساب خود را تغییر دهید">
        <form wire:submit="updatePassword" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="current_password"
                label="رمز فعلی"
                type="password"
                required
                autocomplete="current-password"
                viewable
            />
            <flux:input
                wire:model="password"
                label="رمز جدید"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />
            <flux:input
                wire:model="password_confirmation"
                label="تکرار رمز جدید"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit">ذخیره رمز</flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
