<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')] #[Title('ظاهر')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout heading="ظاهر" subheading="حالت نمایش رابط کاربری را انتخاب کنید">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">روشن</flux:radio>
            <flux:radio value="dark" icon="moon">تیره</flux:radio>
            <flux:radio value="system" icon="computer-desktop">سیستم</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
