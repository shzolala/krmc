<x-layouts.site title="حامیان کوهستان" meta-description="شرکت‌ها و مجموعه‌هایی که با حمایت مالی، توسعه گردشگری و تربیت مربی در کرمان را ممکن می‌کنند.">
    <header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-2xl">
                <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">حامیان کوهستان</h1>
                <p class="mt-2 text-sm leading-7 text-muted">
                    این مجموعه‌ها با حمایت مالی خود، توسعه گردشگری کوهستان و تربیت مربی را در کرمان ممکن کرده‌اند.
                    گزارش هزینه‌کرد منابع برای اعضا در بخش مالی سایت شفاف است.
                </p>
            </div>

            @if ($totalSupport > 0)
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-navy-900/10">
                    <p class="text-xs text-muted">مجموع حمایت‌های دریافت‌شده</p>
                    <p class="mt-0.5 text-xl font-extrabold tabular-nums text-navy-900">{{ fa_num($totalSupport) }} <span class="text-xs font-normal text-muted">تومان</span></p>
                </div>
            @endif
        </div>
    </header>

    <x-site.section spacing="pt-10">
        @if ($sponsors->isEmpty())
            <x-site.empty message="هنوز حامی‌ای ثبت نشده است." :href="route('pricing')" />
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($sponsors as $sponsor)
                    <a href="{{ route('sponsors.show', $sponsor) }}" class="group flex flex-col rounded-3xl bg-white p-6 ring-1 ring-navy-900/10 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                        <div class="flex h-28 items-center justify-center rounded-2xl bg-sand px-6">
                            @if ($sponsor->logo_url)
                                <img src="{{ $sponsor->logo_url }}" alt="لوگوی {{ $sponsor->name }}" loading="lazy" class="max-h-16 w-auto max-w-full object-contain transition duration-500 group-hover:scale-105">
                            @else
                                <span class="text-center font-bold text-navy-900/70">{{ $sponsor->name }}</span>
                            @endif
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-2">
                            <h2 class="font-bold text-navy-900">{{ $sponsor->name }}</h2>
                            @if ($sponsor->is_featured)
                                <span class="rounded-full bg-accent/15 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">حامی ویژه</span>
                            @endif
                        </div>
                        @if ($sponsor->summary)
                            <p class="mt-2 line-clamp-3 text-sm leading-7 text-muted">{{ $sponsor->summary }}</p>
                        @endif

                        <div class="mt-auto flex items-center justify-between gap-2 pt-5 text-xs">
                            @if ($sponsor->show_amount && $sponsor->incomes_sum_amount > 0)
                                <span class="text-muted">حمایت: <strong class="font-bold tabular-nums text-navy-900">{{ fa_num($sponsor->incomes_sum_amount) }}</strong> تومان</span>
                            @else
                                <span></span>
                            @endif
                            <span class="inline-flex items-center gap-1 font-semibold text-navy-900 transition group-hover:text-accent">
                                معرفی <x-site.icon name="arrow-left" class="h-3.5 w-3.5" />
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-12 flex flex-wrap items-center justify-between gap-4 rounded-3xl bg-navy-900 p-6 text-white sm:p-8">
            <div class="max-w-xl">
                <h2 class="text-lg font-extrabold">شما هم می‌توانید حامی کوهستان باشید</h2>
                <p class="mt-1.5 text-sm leading-7 text-white/70">با انتخاب یکی از بسته‌های حمایت، لوگو و معرفی مجموعه‌تان در صفحه اصلی و صفحه حامیان نمایش داده می‌شود.</p>
            </div>
            <x-site.button :href="route('pricing').'#sponsorship'" variant="primary">بسته‌های حمایت</x-site.button>
        </div>
    </x-site.section>
</x-layouts.site>
