<x-layouts.site meta-description="باشگاه‌ها، مربیان، سرپرستان و مقاصد کوهنوردی کرمان در یک پلتفرم شفاف.">
    @php
        $mapCounts = $mapMarkers->countBy('type');
    @endphp

    <x-site.hero-slider :slides="$heroSlides" :ongoing-count="$ongoingProgramsCount" />

    {{-- Upcoming programs --}}
    @if ($upcomingPrograms->isNotEmpty())
        <x-site.section
            spacing="pt-10 sm:pt-14"
            eyebrow="صعود بعدی‌تان"
            title="برنامه‌های پیش‌رو"
            description="نزدیک‌ترین برنامه‌های باشگاه‌های تأییدشده؛ نشان سبز یعنی در حال برگزاری"
            :href="route('programs.index')"
            link-label="همه برنامه‌ها"
        >
            <x-site.swipe-row class="sm:grid-cols-2 sm:gap-5 lg:grid-cols-4" label="برنامه‌های پیش‌رو">
                @foreach ($upcomingPrograms as $program)
                    <x-cards.program :program="$program" />
                @endforeach
            </x-site.swipe-row>
        </x-site.section>
    @endif

    {{-- Clubs and coaches --}}
    <x-site.section spacing="pt-10 sm:pt-14">
        <div data-tabs>
            <x-site.section-heading eyebrow="همراهان مطمئن" title="باشگاه‌ها و سرپرستان تأییدشده" description="استعلام‌شده از سامانه فراکوه">
                <x-slot:actions>
                    <div role="tablist" aria-label="باشگاه‌ها و مربیان" class="inline-flex rounded-full bg-white p-1 ring-1 ring-navy-900/10">
                        @foreach ([
                            'clubs' => ['label' => 'باشگاه‌ها', 'href' => route('clubs.index'), 'more' => 'همه باشگاه‌ها'],
                            'coaches' => ['label' => 'مربیان و سرپرستان', 'href' => route('coaches.index'), 'more' => 'همه پروفایل‌ها'],
                        ] as $key => $tab)
                            <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-tab="{{ $key }}" data-more-href="{{ $tab['href'] }}" data-more-label="{{ $tab['more'] }}" class="rounded-full px-4 py-1.5 text-sm font-bold text-navy-800 transition aria-selected:bg-navy-900 aria-selected:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">{{ $tab['label'] }}</button>
                        @endforeach
                    </div>
                </x-slot:actions>
                <x-slot:link>
                    <a data-tab-more href="{{ route('clubs.index') }}" class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-bold text-navy-900 transition hover:text-accent">
                        <span data-tab-more-label>همه باشگاه‌ها</span> <x-site.icon name="arrow-left" class="h-4 w-4" />
                    </a>
                </x-slot:link>
            </x-site.section-heading>

            <div role="tabpanel" id="panel-clubs" aria-labelledby="tab-clubs" data-tab-panel="clubs">
                <x-site.swipe-row class="sm:grid-cols-2 sm:gap-5 lg:grid-cols-4" label="باشگاه‌ها">
                    @forelse ($featuredClubs as $club)
                        <x-cards.club :club="$club" />
                    @empty
                        <x-site.empty class="col-span-full" message="در حال حاضر باشگاهی برای نمایش انتخاب نشده است." />
                    @endforelse
                </x-site.swipe-row>
            </div>

            <div role="tabpanel" id="panel-coaches" aria-labelledby="tab-coaches" data-tab-panel="coaches" hidden>
                <x-site.swipe-row class="sm:grid-cols-2 sm:gap-5 lg:grid-cols-4" label="مربیان و سرپرستان">
                    @foreach ($featuredCoaches as $coach)
                        <x-cards.coach :coach="$coach" />
                    @endforeach
                </x-site.swipe-row>
            </div>
        </div>
    </x-site.section>

    {{-- Live map --}}
    <section class="mt-12 bg-navy-900 text-white sm:mt-16" aria-labelledby="map-heading">
        <div class="grid lg:grid-cols-[0.8fr_1.2fr]">
            <div class="flex flex-col justify-center px-4 py-8 sm:px-6 sm:py-12 lg:pe-[max(2rem,calc((100vw_-_80rem)/2_+_2rem))] lg:ps-10">
                <p class="text-[11px] font-bold tracking-wide text-accent-soft">نقشه یکپارچه</p>
                <h2 id="map-heading" class="mt-1 text-xl font-extrabold leading-snug sm:text-2xl">مسیرتان را قبل از حرکت ببینید</h2>
                <p class="mt-1 max-w-lg text-xs leading-6 text-white/65 sm:text-sm">قله‌ها و پناهگاه‌ها روی نقشه؛ باشگاه‌ها، اقامتگاه‌ها و فروشگاه‌ها در نقشه کامل</p>
                <ul class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                    <li class="rounded-full bg-white/10 px-3 py-1.5 ring-1 ring-white/15">{{ fa_num($mapCounts['destination'] ?? 0) }} مقصد</li>
                    <li class="rounded-full bg-white/10 px-3 py-1.5 ring-1 ring-white/15">{{ fa_num($mapCounts['shelter'] ?? 0) }} پناهگاه</li>
                </ul>
                <x-site.button :href="route('map')" variant="primary" icon="map" class="mt-5 w-fit !px-5 !py-2.5">نقشه کامل</x-site.button>
            </div>
            <x-geo-map :markers="$mapMarkers" height="h-72 lg:h-[26rem]" :zoom="7" class="rounded-none! ring-0!" />
        </div>
    </section>

    {{-- Explore: regions, attractions, accommodations --}}
    @php
        $exploreTabs = collect([
            'regions' => $featuredRegions->isNotEmpty() ? ['label' => 'مناطق', 'href' => route('regions.index'), 'more' => 'همه مناطق'] : null,
            'attractions' => $featuredAttractions->isNotEmpty() ? ['label' => 'جاذبه‌ها', 'href' => route('attractions.index'), 'more' => 'همه جاذبه‌ها'] : null,
            'accommodations' => $featuredAccommodations->isNotEmpty() ? ['label' => 'اقامتگاه‌ها', 'href' => route('accommodations.index'), 'more' => 'همه اقامتگاه‌ها'] : null,
        ])->filter();
        $firstExplore = $exploreTabs->first();
    @endphp
    @if ($firstExplore)
        <x-site.section spacing="pt-10 sm:pt-14">
            <div data-tabs>
                <x-site.section-heading eyebrow="گردشگری" title="کشف کرمان" description="مناطق، جاذبه‌ها و اقامتگاه‌ها">
                    <x-slot:actions>
                        <div role="tablist" aria-label="کشف کرمان" class="inline-flex rounded-full bg-white p-1 ring-1 ring-navy-900/10">
                            @foreach ($exploreTabs as $key => $tab)
                                <button type="button" role="tab" id="tab-explore-{{ $key }}" aria-controls="panel-explore-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-tab="{{ $key }}" data-more-href="{{ $tab['href'] }}" data-more-label="{{ $tab['more'] }}" class="rounded-full px-4 py-1.5 text-sm font-bold text-navy-800 transition aria-selected:bg-navy-900 aria-selected:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">{{ $tab['label'] }}</button>
                            @endforeach
                        </div>
                    </x-slot:actions>
                    <x-slot:link>
                        <a data-tab-more href="{{ $firstExplore['href'] }}" class="inline-flex items-center gap-1.5 whitespace-nowrap text-sm font-bold text-navy-900 transition hover:text-accent">
                            <span data-tab-more-label>{{ $firstExplore['more'] }}</span> <x-site.icon name="arrow-left" class="h-4 w-4" />
                        </a>
                    </x-slot:link>
                </x-site.section-heading>

                @foreach ($exploreTabs as $key => $tab)
                    <div role="tabpanel" id="panel-explore-{{ $key }}" aria-labelledby="tab-explore-{{ $key }}" data-tab-panel="{{ $key }}" @unless ($loop->first) hidden @endunless>
                        <x-site.swipe-row class="sm:grid-cols-2 sm:gap-5 lg:grid-cols-4" :label="$tab['label']">
                            @if ($key === 'regions')
                                @foreach ($featuredRegions as $region)
                                    <x-cards.region :region="$region" class="min-h-72" />
                                @endforeach
                            @elseif ($key === 'attractions')
                                @foreach ($featuredAttractions as $attraction)
                                    <x-cards.attraction :attraction="$attraction" />
                                @endforeach
                            @else
                                @foreach ($featuredAccommodations as $accommodation)
                                    <x-cards.accommodation :accommodation="$accommodation" />
                                @endforeach
                            @endif
                        </x-site.swipe-row>
                    </div>
                @endforeach
            </div>
        </x-site.section>
    @endif

    {{-- Reports --}}
    <x-site.section
        spacing="pt-10 sm:pt-14"
        eyebrow="از دل کوهستان"
        title="گزارش برنامه‌ها"
        description="روایت صعودهای اخیر باشگاه‌ها"
    >
        <x-site.swipe-row from="md" class="md:grid-cols-3 md:gap-5" label="گزارش برنامه‌ها">
            @forelse ($latestReports as $report)
                <x-cards.report :report="$report" />
            @empty
                <x-site.empty class="md:col-span-3" message="هنوز گزارش برنامه‌ای منتشر نشده است." />
            @endforelse
        </x-site.swipe-row>
    </x-site.section>

    {{-- Shelters --}}
    <section class="mt-12 bg-navy-900 py-10 text-white sm:mt-16 sm:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading
                tone="dark"
                eyebrow="ویژه · نه اقامتگاه تجاری"
                title="پناهگاه‌های کرمان"
                description="نقاط حمایتی مسیرهای کوهستانی"
                :href="route('shelters.index')"
                link-label="همه پناهگاه‌ها"
            />

            <x-site.swipe-row from="md" wide class="md:grid-cols-2" label="پناهگاه‌ها">
                @foreach ($shelters->take(4) as $shelter)
                    <x-cards.shelter :shelter="$shelter" class="min-h-60" />
                @endforeach
            </x-site.swipe-row>
        </div>
    </section>

    {{-- Sponsors --}}
    @if ($sponsors->isNotEmpty())
        <section class="mt-12 sm:mt-16" aria-labelledby="sponsors-heading">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <x-site.section-heading
                    eyebrow="حمایت‌شده توسط"
                    title="حامیان کوهستان"
                    description="توسعه گردشگری و تربیت مربی در کرمان"
                    :href="route('sponsors.index')"
                    link-label="معرفی حامیان"
                    class="!mb-0"
                />
            </div>

            <x-site.sponsor-marquee :sponsors="$sponsors" class="mt-5" />

            @if ($sponsorProjects->isNotEmpty())
                <div class="mx-auto mt-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                    <x-site.swipe-row from="lg" class="lg:grid-cols-4 lg:gap-5" label="طرح‌های حمایتی" :more-href="route('projects.index')" more-label="همه طرح‌ها" more-always>
                        @foreach ($sponsorProjects as $project)
                            <div @class(['lg:hidden' => $loop->index >= 3])>
                                <x-site.project-card :project="$project" />
                            </div>
                        @endforeach
                    </x-site.swipe-row>
                </div>
            @endif
        </section>
    @endif
</x-layouts.site>
