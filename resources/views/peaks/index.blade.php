<x-layouts.site title="بلندترین‌های کرمان" meta-description="نردبان قله‌های کرمان؛ قله‌های صعودشده را ثبت کنید، نشان بگیرید و پله‌پله بالا بروید.">
    <header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
        <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">بلندترین‌های کرمان</h1>
        <p class="mt-2 max-w-2xl text-sm leading-7 text-muted">
            قله‌ها از بلندترین به پایین‌تر چیده شده‌اند. هر قله‌ای که صعود می‌کنید پله‌ی بعدی نردبان و قدمی به سوی نشان‌های تازه است.
        </p>
    </header>

    <div class="mx-auto mt-8 grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:px-8">
        <aside class="order-first space-y-5 lg:order-last lg:sticky lg:top-24 lg:self-start" aria-label="پیشرفت و نشان‌ها">
            <section class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10">
                @auth
                    <div class="flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs text-muted">قله‌های صعودشده</p>
                            <p class="mt-1 text-3xl font-extrabold tabular-nums text-navy-900">
                                {{ fa_num($progress->count()) }}<span class="text-base font-semibold text-muted"> از {{ fa_num($totalPeaks) }}</span>
                            </p>
                        </div>
                        @if ($title)
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $title['badge']->color->classes() }}">
                                <flux:icon :name="$title['badge']->icon" variant="mini" class="size-3.5" /> {{ $title['badge']->name }}
                            </span>
                        @endif
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-navy-900/10" role="progressbar" aria-valuenow="{{ $progress->count() }}" aria-valuemin="0" aria-valuemax="{{ $totalPeaks }}" aria-label="پیشرفت در نردبان قله‌ها">
                        <div class="h-full rounded-full bg-accent" style="width: {{ $totalPeaks > 0 ? round($progress->count() / $totalPeaks * 100, 1) : 0 }}%"></div>
                    </div>
                    @if ($next)
                        <p class="mt-3 text-xs leading-6 text-muted">
                            هدف بعدی: <strong class="text-navy-900">{{ $next['badge']->name }}</strong>
                            — {{ fa_num($next['required'] - $next['current']) }} قله{{ $next['badge']->min_elevation ? ' بالای '.fa_num($next['badge']->min_elevation).' متر' : '' }} دیگر
                        </p>
                    @elseif ($badges->isNotEmpty())
                        <p class="mt-3 text-xs text-muted">همه‌ی نشان‌ها را گرفته‌اید. دست مریزاد!</p>
                    @endif
                @else
                    <p class="text-sm font-bold text-navy-900">پیشرفت خود را دنبال کنید</p>
                    <p class="mt-1.5 text-xs leading-6 text-muted">با ورود به حساب، قله‌های صعودشده روی نردبان تیک می‌خورند و نشان‌های شما ثبت می‌شود.</p>
                    <x-site.button :href="route('login')" variant="dark" class="mt-4 !px-5 !py-2 !text-xs">ورود / ثبت‌نام</x-site.button>
                @endauth
            </section>

            @if ($badges->isNotEmpty())
                <section class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10" aria-labelledby="badges-heading">
                    <h2 id="badges-heading" class="text-sm font-bold text-navy-900">نشان‌های صعود</h2>
                    <ul class="mt-4 space-y-4">
                        @foreach ($badges as $row)
                            <x-climb-badge :row="$row" />
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($leaders->isNotEmpty())
                <section class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10" aria-labelledby="leaders-heading">
                    <h2 id="leaders-heading" class="text-sm font-bold text-navy-900">قله‌نوردان پیشرو</h2>
                    <ol class="mt-4 space-y-3">
                        @foreach ($leaders as $leader)
                            <li class="flex items-center gap-3 text-sm">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-navy-900/5 text-xs font-bold text-navy-800">{{ fa_num($loop->iteration) }}</span>
                                <span class="min-w-0 flex-1 truncate font-medium text-navy-900">{{ $leader['user']->name }}</span>
                                <span class="text-xs tabular-nums text-muted">{{ fa_num($leader['count']) }} قله</span>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </aside>

        <section class="min-w-0" aria-labelledby="ladder-heading">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 id="ladder-heading" class="text-base font-bold text-navy-900">نردبان ارتفاع قله‌ها</h2>
                @auth
                    <nav aria-label="فیلتر قله‌ها" class="flex gap-1 rounded-lg bg-navy-900/5 p-0.5 text-xs font-semibold">
                        @foreach ([null => 'همه', 'todo' => 'صعودنشده', 'done' => 'صعودشده'] as $value => $label)
                            <a href="{{ route('peaks.index', array_filter(['show' => $value])) }}" @class(['rounded-md px-3 py-1.5 transition', 'bg-white text-navy-900 shadow-sm' => $filter === $value, 'text-muted hover:text-navy-900' => $filter !== $value]) @if ($filter === $value) aria-current="true" @endif>{{ $label }}</a>
                        @endforeach
                    </nav>
                @endauth
            </div>
            <p class="mt-1 text-xs text-muted">طول هر نوار، ارتفاع قله از سطح دریاست. ارتفاع‌ها بر پایه‌ی منابع عمومی است و ممکن است با منابع رسمی تفاوت داشته باشد.</p>

            <ol class="mt-5 divide-y divide-white/10 overflow-hidden rounded-3xl bg-navy-900 text-white">
                @forelse ($rows as $row)
                    @php
                        $peak = $row['peak'];
                        $ascent = $row['ascent'];
                        $trail = $peak->trail_info ?? [];
                    @endphp
                    <li>
                        <a href="{{ route('destinations.show', $peak) }}" class="group grid items-center gap-x-5 gap-y-2 px-4 py-3.5 transition hover:bg-white/5 focus:outline-none focus-visible:bg-white/10 sm:px-5 md:grid-cols-[2.25rem_minmax(0,11rem)_1fr_7rem]">
                            <span class="flex items-center gap-3 md:contents">
                                @auth
                                    <span @class([
                                        'flex h-7 w-7 shrink-0 items-center justify-center rounded-full ring-1 ring-inset transition md:order-first',
                                        'bg-accent text-navy-950 ring-accent' => $ascent,
                                        'text-transparent ring-white/25' => ! $ascent,
                                    ]) title="{{ $ascent ? 'صعود شده'.($ascent->climbed_on ? ' — '.fa_date($ascent->climbed_on) : '') : 'هنوز صعود نشده' }}">
                                        <x-site.icon name="check" class="h-3.5 w-3.5" />
                                        <span class="sr-only">{{ $ascent ? 'صعودشده' : 'صعودنشده' }}</span>
                                    </span>
                                @else
                                    <span class="hidden w-7 text-center text-sm font-bold text-white/35 md:block">{{ fa_num($row['rank']) }}</span>
                                @endauth
                                <span class="min-w-0 flex-1 md:flex-none">
                                    <span class="block truncate text-base font-bold transition group-hover:text-accent-soft">{{ $peak->name }}</span>
                                    <span class="block text-xs text-white/55">
                                        @if ($ascent?->climbed_on) صعود در {{ fa_date($ascent->climbed_on) }}
                                        @elseif ($peak->climbers_count > 0) {{ fa_num($peak->climbers_count) }} صعودکننده
                                        @else {{ data_get($trail, 'best_season') ?? 'منتظر نخستین صعود' }}
                                        @endif
                                    </span>
                                </span>
                            </span>
                            <span class="relative h-6 overflow-hidden rounded-md bg-white/5" role="img" aria-label="{{ $peak->name }}، {{ fa_num($peak->elevation) }} متر">
                                <span class="ladder-bar absolute inset-y-0 right-0 rounded-md bg-linear-to-l from-accent to-accent/40" style="width: {{ round($peak->elevation / max(1, $tallest) * 100, 1) }}%"></span>
                                <span class="absolute inset-y-0 right-3 flex items-center text-xs font-extrabold text-navy-950">{{ fa_num($peak->elevation) }} م</span>
                            </span>
                            <span class="hidden md:block"><x-site.difficulty-meter :grade="$peak->difficultyGrade()" tone="dark" /></span>
                        </a>
                    </li>
                @empty
                    <li class="px-5 py-10 text-center text-sm text-white/60">قله‌ای در این فیلتر نیست.</li>
                @endforelse
            </ol>
        </section>
    </div>
</x-layouts.site>
