@props(['title' => null])

@php
    use App\Support\Admin\AdminNavigation;

    $user = auth()->user();
    $navGroups = AdminNavigation::groups();
    $searchIndex = collect($navGroups)
        ->flatMap(fn (array $group) => collect($group['items'])->map(fn (array $item): array => [...$item, 'group' => $group['heading']]))
        ->values();
@endphp

<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="min-h-screen bg-zinc-100 dark:bg-zinc-950">
        <a href="#main-content" class="sr-only z-50 rounded-md bg-white px-4 py-2 text-sm font-medium shadow focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:outline-2 focus:outline-brand">
            پرش به محتوای اصلی
        </a>
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

            <div
                x-data="{ query: '', items: @js($searchIndex), get results() { const q = this.query.trim().toLowerCase(); return q ? this.items.filter(i => (i.label + ' ' + i.group).toLowerCase().includes(q)) : []; } }"
                x-on:keydown.window.slash="if (! ['INPUT', 'TEXTAREA', 'SELECT'].includes($event.target.tagName) && ! $event.target.isContentEditable) { $event.preventDefault(); $refs.search.focus(); }"
                class="flex min-h-0 flex-1 flex-col gap-2"
            >
                <div class="relative">
                    <label for="admin-nav-search" class="sr-only">جستجو در منوی مدیریت</label>
                    <flux:icon.magnifying-glass class="pointer-events-none absolute start-2.5 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
                    <input
                        id="admin-nav-search" x-ref="search" x-model="query" type="search" autocomplete="off"
                        placeholder="جستجو در منو ( / )"
                        x-on:keydown.escape="query = ''; $el.blur()"
                        class="h-9 w-full rounded-lg border border-zinc-200 bg-zinc-50 ps-9 pe-3 text-sm placeholder:text-zinc-400 focus:border-navy-700 focus:bg-white focus:outline-2 focus:outline-offset-1 focus:outline-brand dark:border-zinc-700 dark:bg-zinc-800"
                    >
                </div>

                <div x-show="query.trim() !== ''" style="display: none" role="region" aria-live="polite" aria-label="نتایج جستجو در منو" class="flex flex-col gap-0.5 overflow-y-auto">
                    <template x-for="item in results" :key="item.href">
                        <a :href="item.href" wire:navigate class="flex flex-col rounded-lg px-3 py-2 text-sm hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-brand dark:hover:bg-zinc-800">
                            <span class="font-medium" x-text="item.label"></span>
                            <span class="text-xs text-zinc-500" x-text="item.group"></span>
                        </a>
                    </template>
                    <p x-show="results.length === 0" class="px-3 py-4 text-center text-sm text-zinc-500">موردی پیدا نشد.</p>
                </div>

                <flux:sidebar.nav x-show="query.trim() === ''" aria-label="منوی مدیریت" class="overflow-y-auto">
                    <flux:sidebar.item icon="squares-2x2" :href="route('panel.admin.dashboard')" :current="request()->routeIs('panel.admin.dashboard')" wire:navigate>
                        داشبورد
                    </flux:sidebar.item>

                    @foreach ($navGroups as $group)
                        <flux:sidebar.group expandable :expanded="$group['expanded']" :heading="$group['heading']" :icon="$group['icon']" class="grid">
                            @foreach ($group['items'] as $item)
                                <flux:sidebar.item
                                    :icon="$item['icon']"
                                    :href="$item['href']"
                                    :current="$item['current']"
                                    :badge="$item['badge']"
                                    badge-color="amber"
                                    wire:navigate
                                >
                                    {{ $item['label'] }}
                                </flux:sidebar.item>
                            @endforeach
                        </flux:sidebar.group>
                    @endforeach
                </flux:sidebar.nav>
            </div>

            <nav aria-label="پیوندهای سایت" class="flex flex-col border-t border-zinc-200 pt-2 dark:border-zinc-800">
                <flux:sidebar.item icon="globe-alt" :href="route('home')" target="_blank">مشاهده سایت</flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.edit') || request()->routeIs('security.edit') || request()->routeIs('appearance.edit')" wire:navigate>
                    تنظیمات حساب
                </flux:sidebar.item>
            </nav>

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

        <flux:main id="main-content" tabindex="-1">
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
