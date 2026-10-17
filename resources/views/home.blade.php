<x-layouts.site meta-description="باشگاه‌ها، مربیان، سرپرستان و مقاصد کوهنوردی کرمان در یک پلتفرم شفاف.">
    @php
        $searchField = 'w-full rounded-xl border-0 bg-sand px-3.5 py-3 text-sm text-ink outline-none ring-1 ring-navy-900/10 transition placeholder:text-muted/70 focus:bg-white focus:ring-2 focus:ring-accent';
        $sections = \App\Support\SiteNavigation::sections();
        $bentoDestinations = $popularDestinations->slice(0, 5)->values();
    @endphp

    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-navy-950 text-white">
        <img src="{{ asset('images/covers/hero.jpg') }}" alt="منظره کوهستان" class="absolute inset-0 h-full w-full object-cover">
        <div class="hero-overlay absolute inset-0"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-36 pt-16 sm:px-6 sm:pb-40 sm:pt-24 lg:px-8 lg:pt-28">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-accent-soft ring-1 ring-white/15 backdrop-blur">
                <span class="h-1.5 w-1.5 rounded-full bg-accent"></span>
                کرمان، قطب کوهنوردی جنوب شرق ایران
            </p>
            <h1 class="mt-6 max-w-3xl text-4xl font-extrabold leading-[1.3] text-balance sm:text-5xl lg:text-6xl">
                جامعه کوهنوردان کرمان
            </h1>
            <p class="mt-5 max-w-xl text-base leading-8 text-white/85 sm:text-lg">
                قله بعدی‌تان را پیدا کنید؛ باشگاه و سرپرست معتبر انتخاب کنید و با برنامه‌ای شفاف راهی کوهستان شوید.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-site.button :href="route('destinations.index')" icon="arrow-left" class="flex-row-reverse">کشف مقاصد</x-site.button>
                <x-site.button :href="route('map')" variant="secondary">مشاهده روی نقشه</x-site.button>
            </div>

            <x-site.stat-strip class="mt-12 max-w-3xl" :items="[
                ['value' => fa_num($stats['clubs']), 'label' => 'باشگاه رسمی', 'href' => route('clubs.index')],
                ['value' => fa_num($stats['coaches']), 'label' => 'مربی و سرپرست', 'href' => route('coaches.index')],
                ['value' => fa_num($stats['programs']), 'label' => 'برنامه در حال برگزاری', 'href' => route('programs.index', ['status' => 'ongoing'])],
                ['value' => fa_num($stats['attractions']), 'label' => 'جاذبه گردشگری', 'href' => route('attractions.index')],
            ]" />
        </div>
    </section>

    {{-- Search card overlapping hero --}}
    <section class="relative z-10 mx-auto -mt-24 max-w-7xl px-4 sm:-mt-28 sm:px-6 lg:px-8">
        <form action="{{ route('destinations.index') }}" method="get" role="search" class="rounded-3xl bg-white p-4 shadow-2xl shadow-navy-900/15 ring-1 ring-navy-900/5 sm:p-6">
            <div class="grid gap-3 lg:grid-cols-[1.2fr_1.5fr_auto] lg:items-end">
                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-muted">کجا می‌روید؟</span>
                    <select name="region" class="{{ $searchField }}">
                        <option value="">همه مناطق</option>
                        @foreach ($regionOptions as $region)
                            <option value="{{ $region->slug }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-semibold text-muted">جستجو</span>
                    <input type="search" name="q" placeholder="نام قله یا نقطه طبیعی..." class="{{ $searchField }}">
                </label>

                <button type="submit" class="w-full rounded-xl bg-navy-900 px-8 py-3 text-sm font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 lg:w-auto">
                    جستجو
                </button>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-navy-900/5 pt-4">
                <span class="ml-1 text-xs font-semibold text-muted">محبوب‌ترین‌ها:</span>
                @foreach ($popularDestinations->take(5) as $destination)
                    <a href="{{ route('destinations.show', $destination) }}" class="inline-flex items-center gap-2 rounded-full bg-sand py-1 pl-3 pr-1 text-xs font-medium text-navy-800 transition hover:bg-navy-900 hover:text-white">
                        <img src="{{ $destination->cover_path }}" alt="" class="h-6 w-6 rounded-full object-cover">
                        {{ $destination->name }}
                    </a>
                @endforeach
            </div>
        </form>
    </section>

    {{-- Quick categories --}}
    <x-site.section aria-label="دسته‌بندی‌ها" spacing="pt-16">
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-5">
            @foreach ($sections as $section)
                <x-site.category-tile :section="$section" />
            @endforeach
        </ul>
    </x-site.section>

    {{-- Popular destinations (bento) --}}
    <x-site.section
        eyebrow="مقاصد محبوب"
        title="قله و نقطه طبیعی بعدی‌تان را انتخاب کنید"
        description="از هزار و جوپار تا نقاط طبیعی هر منطقه، همراه با جاذبه‌های نزدیک و باشگاه‌هایی که آن مسیر را می‌روند."
        :href="route('destinations.index')"
        link-label="همه مقاصد"
    >
        <div class="grid gap-4 md:grid-cols-4 md:grid-rows-2">
            @foreach ($bentoDestinations as $destination)
                <x-cards.destination :destination="$destination" :featured="$loop->first" :class="$loop->first ? 'md:col-span-2 md:row-span-2' : ''" />
            @endforeach
        </div>
    </x-site.section>

    {{-- Upcoming programs --}}
    @if ($upcomingPrograms->isNotEmpty())
        <x-site.section
            eyebrow="برنامه‌های پیش‌رو"
            title="به صعود بعدی بپیوندید"
            description="برنامه‌های باشگاه‌های تأییدشده با ظرفیت و تاریخ مشخص."
            :href="route('programs.index')"
            link-label="همه برنامه‌ها"
        >
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($upcomingPrograms as $program)
                    <x-cards.program :program="$program" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Regions --}}
    <x-site.section
        eyebrow="جغرافیا"
        title="مناطق کوهستانی"
        description="قله‌ها و نقاط طبیعی را بر اساس منطقه ببینید؛ کنار هر مسیر، جاذبه‌های نزدیک هم مشخص است."
        :href="route('regions.index')"
        link-label="همه مناطق"
    >
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredRegions as $region)
                <x-cards.region :region="$region" class="min-h-72" />
            @endforeach
        </div>
    </x-site.section>

    {{-- Attractions --}}
    @if ($featuredAttractions->isNotEmpty())
        <x-site.section
            eyebrow="گردشگری"
            title="جاذبه‌های دیدنی کنار مسیر"
            description="باغ‌ها، بناهای تاریخی و طبیعت اطراف کوهستان؛ برای روزهای استراحت و سفرهای خانوادگی."
            :href="route('attractions.index')"
            link-label="همه جاذبه‌ها"
        >
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredAttractions as $attraction)
                    <x-cards.attraction :attraction="$attraction" />
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Map banner --}}
    <x-site.section>
        <div class="relative isolate overflow-hidden rounded-3xl bg-navy-900 px-6 py-12 text-white sm:px-12 sm:py-16">
            <img src="{{ asset('images/covers/hero.jpg') }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30">
            <div class="absolute inset-0 -z-10 bg-gradient-to-l from-navy-950/30 via-navy-950/70 to-navy-950"></div>
            <div class="max-w-xl">
                <p class="text-xs font-bold text-accent-soft">نقشه یکپارچه</p>
                <h2 class="mt-3 text-2xl font-extrabold leading-snug sm:text-4xl">همه چیز را روی نقشه ببینید</h2>
                <p class="mt-4 text-sm leading-8 text-white/80 sm:text-base">
                    قله‌ها، پناهگاه‌ها، اقامتگاه‌ها، باشگاه‌ها و فروشگاه‌ها با موقعیت دقیق جغرافیایی؛ مسیرتان را قبل از حرکت برنامه‌ریزی کنید.
                </p>
                <ul class="mt-6 flex flex-wrap gap-2 text-xs font-medium">
                    @foreach ([['shelters', 'پناهگاه'], ['attractions', 'جاذبه'], ['clubs', 'باشگاه']] as [$key, $label])
                        <li class="rounded-full bg-white/10 px-3 py-1.5 ring-1 ring-white/15">{{ fa_num($stats[$key]) }} {{ $label }}</li>
                    @endforeach
                </ul>
                <x-site.button :href="route('map')" icon="arrow-left" class="mt-8 flex-row-reverse">باز کردن نقشه</x-site.button>
            </div>
        </div>
    </x-site.section>

    {{-- Featured clubs --}}
    <x-site.section
        eyebrow="باشگاه‌های رسمی و تأییدشده"
        title="انتخاب باشگاه‌های برجسته کوهنوردی"
        description="باشگاه‌های دارای مجوز رسمی، مربیان کارآزموده و تقویم صعودهای فصلی."
        :href="route('clubs.index')"
        link-label="مشاهده همه باشگاه‌ها"
    >
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($featuredClubs as $club)
                <x-cards.club :club="$club" />
            @empty
                <x-site.empty class="col-span-full" message="در حال حاضر باشگاهی برای نمایش انتخاب نشده است." />
            @endforelse
        </div>

        <ul class="mt-8 grid gap-4 rounded-3xl bg-white p-6 ring-1 ring-navy-900/5 sm:grid-cols-3">
            @foreach ([
                ['title' => 'مجوز رسمی فدراسیون', 'text' => 'استعلام اطلاعات و تأییدیه باشگاه‌ها از سامانه فراکوه', 'icon' => 'check'],
                ['title' => 'مربیان و سرپرستان معتبر', 'text' => 'هدایت تیم‌ها با راهنمایان باسابقه و دارای احکام فنی', 'icon' => 'users'],
                ['title' => 'شفافیت مالی', 'text' => 'تبلیغات و دیده‌شدن با قیمت و شرایط روشن انجام می‌شود', 'icon' => 'coin'],
            ] as $feature)
                <li class="flex items-center gap-3.5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent/15 text-accent">
                        <x-site.icon :name="$feature['icon']" />
                    </span>
                    <div>
                        <p class="text-sm font-bold text-navy-900">{{ $feature['title'] }}</p>
                        <p class="mt-0.5 text-xs leading-6 text-muted">{{ $feature['text'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </x-site.section>

    {{-- Coaches --}}
    <x-site.section
        eyebrow="افراد کلیدی"
        title="مربیان و سرپرستان"
        description="همراهان مطمئن شما در مسیر؛ با تخصص، سابقه و تأییدیه مشخص."
        :href="route('coaches.index')"
        link-label="همه پروفایل‌ها"
    >
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredCoaches as $coach)
                <x-cards.coach :coach="$coach" />
            @endforeach
        </div>
    </x-site.section>

    {{-- Shelters --}}
    <section class="mt-20 bg-navy-900 py-16 text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading
                tone="dark"
                eyebrow="ویژه · نه اقامتگاه تجاری"
                title="پناهگاه‌های کرمان"
                description="نقاط حمایتی مسیرهایی مثل هزار و جوپار، جدا از بوم‌گردی و هتل و با موقعیت جغرافیایی روی نقشه."
                :href="route('shelters.index')"
                link-label="همه پناهگاه‌ها"
            />

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($shelters->take(4) as $shelter)
                    <x-cards.shelter :shelter="$shelter" class="min-h-60" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Accommodations --}}
    <x-site.section
        eyebrow="اقامت"
        title="اقامتگاه‌ها"
        description="بوم‌گردی، هتل، مهمان‌سرا و کمپ، همه با موقعیت جغرافیایی برای نمایش روی نقشه."
        :href="route('accommodations.index')"
        link-label="همه اقامتگاه‌ها"
    >
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredAccommodations as $accommodation)
                <x-cards.accommodation :accommodation="$accommodation" />
            @endforeach
        </div>
    </x-site.section>

    {{-- Reports --}}
    <x-site.section
        eyebrow="از دل کوهستان"
        title="گزارش برنامه‌ها"
        description="روایت صعودها و برنامه‌های اخیر باشگاه‌ها؛ پیش از انتخاب مسیر، تجربه دیگران را بخوانید."
    >
        <div class="grid gap-5 md:grid-cols-3">
            @forelse ($latestReports as $report)
                <x-cards.report :report="$report" />
            @empty
                <x-site.empty class="md:col-span-3" message="هنوز گزارش برنامه‌ای منتشر نشده است." />
            @endforelse
        </div>
    </x-site.section>

    {{-- Final CTA --}}
    <x-site.section>
        <x-site.cta
            title="باشگاه، مربی یا کسب‌وکار کوهستانی دارید؟"
            description="پروفایل رسمی بسازید، برنامه‌هایتان را منتشر کنید و در جستجوی کوهنوردان دیده شوید. هر نقش، پنل و داشبورد اختصاصی خودش را دارد."
        >
            @guest
                <x-site.button :href="route('login')" variant="dark">ورود / ثبت‌نام</x-site.button>
            @else
                <x-site.button :href="route(auth()->user()->panelHomeRoute())" variant="dark">ورود به پنل من</x-site.button>
            @endguest
            <x-site.button :href="route('clubs.index')" variant="light">مشاهده باشگاه‌ها</x-site.button>
        </x-site.cta>
    </x-site.section>
</x-layouts.site>
