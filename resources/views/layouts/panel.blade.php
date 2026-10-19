@props(['title' => null])

@php
    use App\Support\PanelNavigation;

    $user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="min-h-screen bg-zinc-100 dark:bg-zinc-950">
        <x-admin.impersonation-banner />

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route($user->panelHomeRoute()) }}" wire:navigate class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-navy-900 text-brand">
                        <flux:icon :name="$user->mainRoles()->count() === 1 ? $user->mainRoles()->first()->icon() : 'squares-2x2'" class="size-5" />
                    </span>
                    <span class="grid leading-tight">
                        <span class="text-sm font-bold">{{ config('app.name') }}</span>
                        <span class="text-xs text-zinc-500">{{ $user->panelTitle() }}</span>
                    </span>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @foreach (PanelNavigation::groups($user) as $group)
                    <flux:sidebar.group :heading="$group['heading']" class="grid">
                        @foreach ($group['items'] as $item)
                            @if ($item['external'])
                                <flux:sidebar.item :icon="$item['icon']" :href="$item['href']" target="_blank">
                                    {{ $item['label'] }}
                                </flux:sidebar.item>
                            @else
                                <flux:sidebar.item :icon="$item['icon']" :href="$item['href']" :current="$item['current']" wire:navigate>
                                    {{ $item['label'] }}
                                </flux:sidebar.item>
                            @endif
                        @endforeach
                    </flux:sidebar.group>
                @endforeach
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="$user->name" />
        </flux:sidebar>

        <flux:header class="border-b border-zinc-200 bg-white lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <span class="text-sm font-bold">{{ $user->panelTitle() }}</span>
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="$user->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>تنظیمات</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            خروج
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
                <x-admin.flash />
                {{ $slot }}
            </div>
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
