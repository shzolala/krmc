<!DOCTYPE html>
<html lang="fa" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ filled($title ?? null) ? $title.' - '.config('app.name', 'KRMC') : config('app.name', 'KRMC') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-sand font-sans text-ink antialiased">
        <div class="grid min-h-svh lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
            <div class="relative flex flex-col justify-center px-6 py-10 sm:px-12">
                <a href="{{ route('home') }}" class="absolute start-6 top-6 flex items-center gap-2.5 sm:start-12" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-900 text-sm font-bold text-white">KR</span>
                    <span class="leading-tight">
                        <span class="block text-sm font-bold text-navy-900">{{ config('app.name', 'KRMC') }}</span>
                        <span class="block text-[11px] text-muted">جامعه کوهنوردان کرمان</span>
                    </span>
                </a>

                <div class="mx-auto mt-16 w-full max-w-sm lg:mt-0">
                    {{ $slot }}
                </div>
            </div>

            <aside class="relative hidden overflow-hidden bg-navy-950 lg:block">
                <img src="{{ asset('images/covers/cover-03.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-45 grayscale-[30%]">
                <div class="absolute inset-0 bg-linear-to-t from-navy-950 via-navy-950/80 to-navy-900/60"></div>
                <div class="pointer-events-none absolute inset-8 border border-brand/30"></div>

                <div class="relative z-10 flex h-full flex-col justify-between p-16 text-white xl:p-20">
                    <div class="flex items-center gap-3 text-xs font-medium tracking-widest text-brand-soft">
                        <span class="h-px w-10 bg-brand"></span>
                        {{ config('app.name', 'KRMC') }}
                    </div>

                    <div class="space-y-6">
                        <h2 class="text-3xl font-extrabold leading-relaxed text-balance xl:text-4xl">
                            پلتفرم رسمی جامعه کوهنوردان کرمان
                        </h2>
                        <p class="max-w-md text-base leading-9 text-white/70">
                            مدیریت عضویت، برنامه‌های صعود و ارتباط با باشگاه‌ها و مربیان، در یک سامانه‌ی یکپارچه.
                        </p>
                        <div class="flex items-center gap-4 border-t border-white/15 pt-6 text-sm text-white/60">
                            <span>باشگاه‌ها</span><span class="h-1 w-1 rounded-full bg-brand"></span>
                            <span>مربیان</span><span class="h-1 w-1 rounded-full bg-brand"></span>
                            <span>برنامه‌ها</span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
