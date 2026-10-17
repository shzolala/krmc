@props(['title' => null])

@php
    use App\Support\Admin\AdminResources;
    use Modules\Review\Models\Review;

    $user = auth()->user();
    $pendingReviews = Review::query()->pending()->count();
    $current = request()->route('resource');
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
                <a href="{{ route('panel.admin.dashboard') }}" wire:navigate class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-navy-900 text-brand">
                        <flux:icon.shield-check class="size-5" />
                    </span>
                    <span class="grid leading-tight">
                        <span class="text-sm font-bold">{{ config('app.name') }}</span>
                        <span class="text-xs text-zinc-500">مرکز مدیریت سایت</span>
                    </span>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="squares-2x2" :href="route('panel.admin.dashboard')" :current="request()->routeIs('panel.admin.dashboard')" wire:navigate>
                    داشبورد
                </flux:sidebar.item>

                <flux:sidebar.group heading="کاربران و دسترسی" class="grid">
                    <flux:sidebar.item icon="users" :href="route('panel.admin.users.index')" :current="request()->routeIs('panel.admin.users.*')" wire:navigate>
                        کاربران
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="key" :href="route('panel.admin.roles')" :current="request()->routeIs('panel.admin.roles')" wire:navigate>
                        نقش‌ها و دسترسی‌ها
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="chat-bubble-left-right"
                        :href="route('panel.admin.reviews')"
                        :current="request()->routeIs('panel.admin.reviews')"
                        :badge="$pendingReviews > 0 ? fa_num($pendingReviews) : null"
                        badge-color="amber"
                        wire:navigate
                    >
                        نظرات کاربران
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @foreach (AdminResources::grouped() as $group => $resources)
                    <flux:sidebar.group :heading="AdminResources::GROUPS[$group]" class="grid">
                        @foreach ($resources as $resource)
                            <flux:sidebar.item
                                :icon="$resource->icon()"
                                :href="route('panel.admin.resources.index', $resource->key())"
                                :current="$current === $resource->key()"
                                wire:navigate
                            >
                                {{ $resource->plural() }}
                            </flux:sidebar.item>
                        @endforeach
                    </flux:sidebar.group>
                @endforeach

                <flux:sidebar.group heading="سایت" class="grid">
                    <flux:sidebar.item icon="globe-alt" :href="route('home')" target="_blank">
                        مشاهده سایت
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.edit') || request()->routeIs('security.edit') || request()->routeIs('appearance.edit')" wire:navigate>
                        تنظیمات حساب
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="$user->name" />
        </flux:sidebar>

        <flux:header class="border-b border-zinc-200 bg-white lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <span class="text-sm font-bold">مرکز مدیریت</span>
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
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
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
