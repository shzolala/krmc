@php
    use App\Support\PanelRegistry;

    $user = auth()->user();
    $panel = $user ? PanelRegistry::forRole($user->role) : null;
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-admin.impersonation-banner />

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ $user ? route($user->panelHomeRoute()) : route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group heading="پنل فعلی" class="grid">
                    <flux:sidebar.item
                        icon="home"
                        :href="route($user->panelHomeRoute())"
                        :current="request()->routeIs('panel.'.$user->role->value.'.*') || request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        داشبورد {{ $user->roleLabel() }}
                    </flux:sidebar.item>

                    @if ($user->isAdmin())
                        <flux:sidebar.item
                            icon="users"
                            :href="route('panel.admin.users.index')"
                            :current="request()->routeIs('panel.admin.users.*')"
                            wire:navigate
                        >
                            کاربران
                        </flux:sidebar.item>
                        <flux:sidebar.item
                            icon="chat-bubble-left-right"
                            :href="route('panel.admin.reviews')"
                            :current="request()->routeIs('panel.admin.reviews')"
                            wire:navigate
                        >
                            نظرات
                        </flux:sidebar.item>
                    @endif

                    @if ($user->hasRole(\App\Enums\UserRole::Club))
                        <flux:sidebar.item
                            icon="building-office-2"
                            :href="route('panel.club.profile')"
                            :current="request()->routeIs('panel.club.profile')"
                            wire:navigate
                        >
                            ویرایش باشگاه
                        </flux:sidebar.item>
                    @endif

                    @if ($user->hasRole(\App\Enums\UserRole::Coach, \App\Enums\UserRole::Leader))
                        <flux:sidebar.item
                            icon="academic-cap"
                            :href="route('panel.'.$user->role->value.'.profile')"
                            :current="request()->routeIs('panel.'.$user->role->value.'.profile')"
                            wire:navigate
                        >
                            پروفایل تخصصی
                        </flux:sidebar.item>
                    @endif

                    @if ($user->hasRole(\App\Enums\UserRole::Shop))
                        <flux:sidebar.item
                            icon="shopping-bag"
                            :href="route('panel.shop.profile')"
                            :current="request()->routeIs('panel.shop.profile')"
                            wire:navigate
                        >
                            ویرایش فروشگاه
                        </flux:sidebar.item>
                    @endif

                    <flux:sidebar.item
                        icon="cog-6-tooth"
                        :href="route('profile.edit')"
                        :current="request()->routeIs('profile.edit') || request()->routeIs('security.edit') || request()->routeIs('appearance.edit')"
                        wire:navigate
                    >
                        تنظیمات حساب
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="سایت عمومی" class="grid">
                    <flux:sidebar.item icon="globe-alt" :href="route('home')" wire:navigate>
                        صفحه اصلی
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map" :href="route('map')" wire:navigate>
                        نقشه
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            @if ($panel)
                <div class="px-4 pb-3 text-xs text-zinc-500" dir="ltr">
                    {{ $panel['path'] }}
                </div>
            @endif

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate" dir="ltr">{{ auth()->user()->mobile }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
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

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
