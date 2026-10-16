<x-layouts.site meta-description="باشگاه‌ها، مربیان، سرپرستان و مقاصد کوهنوردی کرمان در یک پلتفرم شفاف.">
    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-navy-900 text-white">
        <img
            src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2000&q=80"
            alt="منظره کوهستان"
            class="absolute inset-0 h-full w-full object-cover"
        >
        <div class="hero-overlay absolute inset-0"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-28 pt-16 sm:px-6 sm:pb-32 sm:pt-20 lg:px-8 lg:pt-24">
            <p class="mb-4 text-xs font-semibold tracking-[0.25em] text-accent-soft">KRMC · KERMAN</p>
            <h1 class="max-w-2xl text-4xl font-extrabold leading-[1.25] text-balance sm:text-5xl lg:text-6xl">
                جامعه کوهنوردان کرمان
            </h1>
            <p class="mt-5 max-w-xl text-base leading-8 text-white/85 sm:text-lg">
                باشگاه‌ها، مربیان، سرپرستان و مقاصد تخصصی را در یک پلتفرم پیدا کنید؛ گزارش برنامه‌ها برای دیده‌شدن در گوگل، با شفافیت مالی تبلیغات.
            </p>

            <div class="mt-10 flex flex-wrap gap-8 text-white">
                <div>
                    <p class="text-3xl font-bold">{{ number_format($stats['clubs']) }}+</p>
                    <p class="mt-1 text-xs tracking-wide text-white/70">باشگاه فعال</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ number_format($stats['coaches']) }}+</p>
                    <p class="mt-1 text-xs tracking-wide text-white/70">مربی و سرپرست</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ number_format($stats['destinations']) }}+</p>
                    <p class="mt-1 text-xs tracking-wide text-white/70">مقصد تخصصی</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Search --}}
    <section class="relative z-10 mx-auto -mt-14 max-w-7xl px-4 sm:-mt-16 sm:px-6 lg:px-8">
        <form action="{{ route('destinations.index') }}" method="get" class="rounded-2xl bg-white p-4 shadow-xl shadow-navy-900/10 ring-1 ring-navy-900/5 sm:p-5">
            <div class="grid gap-3 lg:grid-cols-[1.1fr_1.4fr_1.1fr_auto]">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-muted">مقصد</span>
                    <select name="destination" class="w-full rounded-xl border-0 bg-sand px-3 py-3 text-sm text-ink outline-none ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent">
                        <option value="">همه مقاصد</option>
                        @foreach ($popularDestinations as $destination)
                            <option value="{{ $destination->slug }}">{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-muted">جستجو</span>
                    <input
                        type="search"
                        name="q"
                        placeholder="باشگاه، مربی، قله یا برنامه..."
                        class="w-full rounded-xl border-0 bg-sand px-3 py-3 text-sm text-ink outline-none ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent"
                    >
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-muted">نقش / خدمت</span>
                    <select name="type" class="w-full rounded-xl border-0 bg-sand px-3 py-3 text-sm text-ink outline-none ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent">
                        <option value="">همه موارد</option>
                        <option value="club">باشگاه</option>
                        <option value="coach">مربی</option>
                        <option value="leader">سرپرست</option>
                        <option value="accommodation">اقامتگاه</option>
                        <option value="shelter">پناهگاه</option>
                        <option value="shop">فروشگاه</option>
                    </select>
                </label>

                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-accent px-6 py-3 text-sm font-bold text-navy-950 transition hover:bg-accent-soft lg:min-w-28">
                        جستجو
                    </button>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-navy-900/5 pt-4">
                <span class="text-[11px] font-semibold tracking-wide text-muted">محبوب الآن</span>
                @foreach ($popularDestinations->take(5) as $destination)
                    <a href="{{ route('destinations.show', $destination) }}" class="inline-flex items-center gap-2 rounded-full bg-sand px-3 py-1.5 text-xs font-medium text-navy-800 transition hover:bg-navy-900 hover:text-white">
                        <span class="h-5 w-5 overflow-hidden rounded-full bg-navy-200">
                            <img src="{{ $destination->cover_path }}" alt="" class="h-full w-full object-cover">
                        </span>
                        {{ $destination->name }}
                        <span class="text-muted">{{ $destination->guides_count }}</span>
                    </a>
                @endforeach
            </div>
        </form>
    </section>

    {{-- Intro --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <p class="text-xs font-semibold tracking-[0.2em] text-muted">چه کسانی اینجا هستند</p>
        <h2 class="mt-3 max-w-2xl text-3xl font-extrabold text-navy-900 sm:text-4xl">بهترین دسترسی به اکوسیستم کوهنوردی کرمان</h2>
        <div class="mt-6 grid gap-8 text-sm leading-8 text-muted md:grid-cols-2">
            <p>
                از باشگاه و مربی تا سرپرست و فروشگاه — همه در یک دایرکتوری شفاف جمع شده‌اند. مقاصد تخصصی تعریف می‌شوند و مشخص است کدام باشگاه یا مربی آن مسیر را می‌برد.
            </p>
            <p>
                فعلاً تمرکز روی دیده‌شدن از طریق گزارش برنامه و پروفایل‌های عمومی است. در آینده، دوره‌های فدراسیون، پرداخت قسطی و پلن‌های عضویت روی همین بستر اضافه می‌شود.
            </p>
        </div>
    </section>

    {{-- Featured clubs --}}
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_1.4fr] lg:items-end">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-accent-soft">دایرکتوری</p>
                    <h2 class="mt-3 text-3xl font-extrabold sm:text-4xl">باشگاه‌ها و برگزارکنندگان برنامه</h2>
                </div>
                <p class="text-sm leading-8 text-white/75">
                    از مسیرهای فنی هزار تا برنامه‌های آموزشی باشگاهی — باشگاه‌های تأییدشده با نشان ویژه و پلن نمایش بیشتر دیده می‌شوند؛ شفاف و قابل ردیابی.
                </p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[0.9fr_1.4fr]">
            <div class="lg:pt-2">
                <h3 class="text-2xl font-bold text-navy-900">انتخاب باشگاه‌های برجسته</h3>
                <p class="mt-4 text-sm leading-8 text-muted">
                    هر باشگاه پروفایل اختصاصی، تعداد اعضا، برنامه‌ها و مربیان مرتبط دارد. عضویت کوهنوردان در باشگاه قابل نمایش است.
                </p>
                <a href="{{ route('clubs.index') }}" class="mt-6 inline-flex rounded-full border border-navy-900/15 px-5 py-2.5 text-sm font-semibold text-navy-900 transition hover:border-accent hover:text-accent">
                    مشاهده همه باشگاه‌ها
                </a>
            </div>

            <div class="space-y-4">
                @forelse ($featuredClubs as $club)
                    <article class="rounded-2xl border border-navy-900/8 bg-white p-5 transition hover:border-accent/40 hover:shadow-lg hover:shadow-navy-900/5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-900 text-sm font-bold text-white">
                                    {{ mb_substr($club->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-navy-900">
                                        <a href="{{ route('clubs.show', $club) }}" class="hover:text-accent">{{ $club->name }}</a>
                                    </h4>
                                    <p class="mt-1 text-xs text-muted">{{ $club->city }} · از {{ $club->founded_year ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @if ($club->is_premium)
                                    <x-badge tone="premium">همکار ویژه</x-badge>
                                @endif
                                @if ($club->is_featured)
                                    <x-badge tone="featured">ویژه</x-badge>
                                @endif
                                @if ($club->is_verified)
                                    <x-badge tone="verified">تأییدشده</x-badge>
                                @endif
                            </div>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-7 text-muted">{{ $club->summary }}</p>
                        <div class="mt-4 flex items-center justify-between gap-3 text-xs text-muted">
                            <span>{{ number_format($club->member_count) }} عضو · {{ number_format($club->program_count) }} برنامه</span>
                            <a href="{{ route('clubs.show', $club) }}" class="font-semibold text-accent">مشاهده پروفایل ←</a>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl bg-white p-8 text-sm text-muted ring-1 ring-navy-900/5">هنوز باشگاهی ثبت نشده است.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Destinations grid --}}
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="flex flex-col justify-between rounded-2xl bg-white p-6 ring-1 ring-navy-900/5 sm:min-h-56">
                <div>
                    <h2 class="text-2xl font-extrabold text-navy-900">مقصد کوهنوردی خود را انتخاب کنید</h2>
                    <p class="mt-3 text-sm leading-7 text-muted">راهنمای انتخاب باشگاه و مربی برای قله‌ها و مسیرهای کرمان</p>
                    <div class="section-slash"></div>
                </div>
                <a href="{{ route('destinations.index') }}" class="mt-6 text-sm font-bold text-accent">همه مقاصد ←</a>
            </div>

            @foreach ($popularDestinations->take(7) as $destination)
                <a href="{{ route('destinations.show', $destination) }}" class="group relative min-h-56 overflow-hidden rounded-2xl">
                    <img src="{{ $destination->cover_path }}" alt="{{ $destination->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/90 via-navy-950/25 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                        <p class="text-[11px] font-semibold tracking-wide text-white/80">{{ $destination->guides_count }} راهنما</p>
                        <p class="mt-1 text-xl font-bold">{{ $destination->name }}</p>
                        <p class="mt-2 text-xs font-semibold text-accent-soft">جزئیات بیشتر</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Shelters (special, not accommodations) --}}
    <section class="bg-amber-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-[0.25em] text-accent-soft">ویژه · نه اقامتگاه</p>
                    <h2 class="mt-2 text-3xl font-extrabold">پناهگاه‌های کرمان</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-8 text-white/75">نقاط حمایتی مسیرهایی مثل هزار و جوپار — جدا از بوم‌گردی و هتل، با موقعیت جغرافیایی روی نقشه.</p>
                </div>
                <a href="{{ route('shelters.index') }}" class="text-sm font-bold text-accent-soft">همه پناهگاه‌ها ←</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($shelters as $shelter)
                    <a href="{{ route('shelters.show', $shelter) }}" class="group relative min-h-52 overflow-hidden rounded-3xl ring-1 ring-white/10">
                        <img src="{{ $shelter->cover_path }}" alt="{{ $shelter->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-amber-950 via-amber-950/50 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5">
                            <p class="text-[11px] font-semibold text-accent-soft">پناهگاه کوهستانی</p>
                            <h3 class="mt-1 text-2xl font-extrabold">{{ $shelter->name }}</h3>
                            <p class="mt-2 text-xs text-white/75">
                                {{ $shelter->destination?->name }}
                                @if ($shelter->elevation)
                                    · {{ number_format($shelter->elevation) }} متر
                                @endif
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Accommodations --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-muted">اقامت</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-navy-900">اقامتگاه‌ها</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-8 text-muted">بوم‌گردی، هتل، مهمان‌سرا و کمپ — همه با لوکیشن جغرافیایی برای نمایش روی نقشه.</p>
                </div>
                <a href="{{ route('accommodations.index') }}" class="text-sm font-bold text-accent">همه اقامتگاه‌ها ←</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($featuredAccommodations as $accommodation)
                    <article class="overflow-hidden rounded-2xl bg-white ring-1 ring-navy-900/5">
                        @if ($accommodation->cover_path)
                            <img src="{{ $accommodation->cover_path }}" alt="" class="h-36 w-full object-cover">
                        @endif
                        <div class="p-4">
                            <p class="text-[11px] font-semibold text-accent">{{ $accommodation->type->label() }}</p>
                            <h3 class="mt-1 text-base font-bold text-navy-900">
                                <a href="{{ route('accommodations.show', $accommodation) }}" class="hover:text-accent">{{ $accommodation->name }}</a>
                            </h3>
                            <p class="mt-2 line-clamp-2 text-xs leading-6 text-muted">{{ $accommodation->summary }}</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('map') }}" class="inline-flex rounded-full bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-800">
                    مشاهده همه روی نقشه
                </a>
            </div>
        </div>
    </section>

    {{-- Coaches --}}
    <section class="bg-white/60 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-muted">افراد کلیدی</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-navy-900">مربیان و سرپرستان</h2>
                </div>
                <a href="{{ route('coaches.index') }}" class="text-sm font-bold text-accent">همه پروفایل‌ها ←</a>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($featuredCoaches as $coach)
                    <article class="rounded-2xl border border-navy-900/8 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-navy-900">
                                    <a href="{{ route('coaches.show', $coach) }}" class="hover:text-accent">{{ $coach->user->name }}</a>
                                </h3>
                                <p class="mt-1 text-xs text-muted">
                                    {{ $coach->roleLabel() }}
                                    @if ($coach->club)
                                        · {{ $coach->club->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex flex-wrap justify-end gap-1.5">
                                @if ($coach->is_leader)
                                    <x-badge tone="leader">سرپرست</x-badge>
                                @endif
                                @if ($coach->is_verified)
                                    <x-badge tone="verified">تأییدشده</x-badge>
                                @endif
                                @if ($coach->is_premium)
                                    <x-badge tone="premium">ویژه</x-badge>
                                @endif
                            </div>
                        </div>
                        <p class="mt-3 line-clamp-2 text-sm leading-7 text-muted">{{ $coach->bio }}</p>
                        <div class="mt-4 flex justify-between text-xs text-muted">
                            <span>{{ $coach->specialty }}</span>
                            <a href="{{ route('coaches.show', $coach) }}" class="font-semibold text-accent">پروفایل ←</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Reports / SEO --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-xs font-semibold tracking-[0.2em] text-muted">سئو و محتوا</p>
            <h2 class="mt-2 text-3xl font-extrabold text-navy-900">گزارش برنامه‌ها</h2>
            <p class="mt-3 max-w-2xl text-sm leading-8 text-muted">محتوای برنامه‌ها پایه دیده‌شدن در گوگل است؛ بعداً همین مسیر به ثبت‌نام دوره و برنامه‌های باشگاهی وصل می‌شود.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @forelse ($latestReports as $report)
                <article class="overflow-hidden rounded-2xl bg-white ring-1 ring-navy-900/5">
                    @if ($report->cover_path)
                        <img src="{{ $report->cover_path }}" alt="" class="h-40 w-full object-cover">
                    @endif
                    <div class="p-5">
                        <p class="text-xs text-muted">
                            {{ $report->destination?->name }}
                            @if ($report->club)
                                · {{ $report->club->name }}
                            @endif
                        </p>
                        <h3 class="mt-2 text-lg font-bold text-navy-900">{{ $report->title }}</h3>
                        <p class="mt-2 line-clamp-3 text-sm leading-7 text-muted">{{ $report->excerpt }}</p>
                    </div>
                </article>
            @empty
                <p class="text-sm text-muted md:col-span-3">هنوز گزارش برنامه‌ای منتشر نشده است.</p>
            @endforelse
        </div>
    </section>

    {{-- CTA roles --}}
    <section class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl bg-navy-900 px-6 py-10 text-white sm:px-10">
            <div class="grid gap-8 lg:grid-cols-[1.3fr_1fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-accent-soft">نقش‌ها و پنل‌ها</p>
                    <h2 class="mt-3 text-3xl font-extrabold">هر گروه، دسترسی و پروفایل خودش را دارد</h2>
                    <p class="mt-4 max-w-xl text-sm leading-8 text-white/75">
                        کوهنورد، باشگاه، مربی، سرپرست، فروشگاه و ارگان‌ها — هر کدام داشبورد جداگانه خواهند داشت. تبلیغات پولی با شفافیت مالی و پلن نمایش متفاوت برای باشگاه و مربی تعریف می‌شود.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @foreach (['کوهنورد', 'باشگاه', 'مربی', 'سرپرست', 'فروشگاه', 'ارگان'] as $role)
                        <div class="rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">{{ $role }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
