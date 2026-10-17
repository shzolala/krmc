@props(['title' => null])

@if (auth()->user()?->isAdmin())
    <x-layouts::admin :title="$title">
        {{ $slot }}
    </x-layouts::admin>
@else
    <x-layouts::panel :title="$title">
        {{ $slot }}
    </x-layouts::panel>
@endif
