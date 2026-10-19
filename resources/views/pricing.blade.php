@php
    use App\Models\MembershipPlan;

    $termLabels = MembershipPlan::TERM_LABELS;
@endphp

<x-layouts.site title="تعرفه عضویت" meta-description="پلن‌ها و تعرفه عضویت در انجمن به تفکیک هر گروه؛ مدت‌های یک‌ماهه، شش‌ماهه و یک‌ساله.">
    <header class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">
        <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">تعرفه عضویت</h1>
        <p class="mt-2 max-w-2xl text-sm leading-7 text-muted">
            حق عضویت صرف توسعه گردشگری و تربیت مربی می‌شود و گزارش مصرف آن برای اعضا شفاف است.
            گروه خود را انتخاب کنید و مدت عضویت را تعیین کنید؛ مدت‌های بلندتر تخفیف بیشتری دارند.
        </p>
    </header>

    <div data-tabs data-pricing data-term="12" class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 border-b border-navy-900/10 sm:flex-row sm:items-end sm:justify-between">
            <div role="tablist" aria-label="گروه عضویت" class="-mb-px flex gap-6 overflow-x-auto">
                @foreach ($groups as $group)
                    <button type="button" role="tab" id="tab-{{ $group['role']->value }}" aria-controls="panel-{{ $group['role']->value }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-tab="{{ $group['role']->value }}"
                        class="shrink-0 border-b-2 border-transparent pb-3 text-sm font-semibold text-muted transition hover:text-navy-900 aria-selected:border-navy-900 aria-selected:text-navy-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                        {{ $group['role']->label() }}
                    </button>
                @endforeach
            </div>

            <div role="radiogroup" aria-label="مدت عضویت" class="mb-3 flex w-fit gap-0.5 rounded-lg bg-navy-900/5 p-0.5">
                @foreach ($termLabels as $months => $label)
                    <button type="button" role="radio" data-term-btn="{{ $months }}" aria-checked="{{ $months === 12 ? 'true' : 'false' }}"
                        class="rounded-md px-3.5 py-1.5 text-xs font-semibold text-muted transition hover:text-navy-900 aria-checked:bg-white aria-checked:text-navy-900 aria-checked:shadow-sm disabled:cursor-not-allowed disabled:opacity-35 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="pt-8">
            @foreach ($groups as $group)
                @php $offered = $group['plans']->flatMap(fn ($plan) => $plan->offeredTerms())->unique()->sort()->values(); @endphp
                <div role="tabpanel" id="panel-{{ $group['role']->value }}" aria-labelledby="tab-{{ $group['role']->value }}"
                    data-tab-panel="{{ $group['role']->value }}" data-offered="{{ $offered->implode(',') }}" @unless ($loop->first) hidden @endunless>
                    <div class="grid items-stretch gap-4 lg:grid-cols-3">
                        @foreach ($group['plans'] as $plan)
                            @php $offeredTerms = $plan->offeredTerms(); @endphp
                            <article data-plan data-terms="{{ implode(',', $offeredTerms) }}" class="group/plan flex flex-col rounded-2xl border border-navy-900/10 bg-white p-6 transition data-[na=true]:bg-transparent">
                                <h2 class="text-base font-bold text-navy-900">{{ $plan->name }}</h2>
                                @if ($plan->description)
                                    <p class="mt-1 text-sm leading-6 text-muted">{{ $plan->description }}</p>
                                @endif

                                <div class="mt-6 min-h-[4.25rem]">
                                    @foreach ($termLabels as $months => $label)
                                        @php
                                            $term = $plan->termPrice($months);
                                            $isOffered = in_array($months, $offeredTerms, true);
                                        @endphp
                                        <div data-term-block="{{ $months }}" @unless ($months === 12) hidden @endunless>
                                            <div class="flex items-baseline justify-between gap-3">
                                                <p @class(['flex items-baseline gap-1.5', 'opacity-40' => ! $isOffered])>
                                                    <span @class(['text-3xl font-extrabold tabular-nums tracking-tight text-navy-900', 'line-through decoration-1' => ! $isOffered])>{{ fa_num($term['final']) }}</span>
                                                    <span class="text-xs text-muted">تومان</span>
                                                </p>
                                                @if (! $isOffered)
                                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-md bg-navy-900/5 px-2 py-0.5 text-xs font-semibold text-muted">
                                                        <flux:icon.lock-closed variant="mini" class="size-3" /> غیرفعال
                                                    </span>
                                                @elseif ($term['discount'] > 0)
                                                    <span class="shrink-0 rounded-md bg-navy-900/5 px-2 py-0.5 text-xs font-semibold text-navy-900">{{ fa_num($term['discount']) }}٪ تخفیف</span>
                                                @endif
                                            </div>
                                            <p class="mt-1.5 text-xs text-muted">{{ $label }} · معادل ماهانه {{ fa_num($term['per_month']) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <ul class="mt-6 flex-1 space-y-2.5 border-t border-navy-900/10 pt-6 text-sm text-ink group-data-[na=true]/plan:text-muted">
                                    @foreach ($plan->features ?? [] as $feature)
                                        <li class="flex items-start gap-2.5">
                                            <x-site.icon name="check" class="mt-1 h-3.5 w-3.5 shrink-0 text-navy-700 group-data-[na=true]/plan:opacity-40" />
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <a href="{{ auth()->check() ? route('panel.membership') : route('login') }}"
                                    class="mt-6 inline-flex w-fit items-center gap-1.5 text-sm font-semibold text-navy-900 transition hover:text-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent group-data-[na=true]/plan:hidden">
                                    {{ auth()->check() ? 'انتخاب در پنل من' : 'ورود و عضویت' }}
                                    <x-site.icon name="arrow-left" class="h-4 w-4" />
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @if ($groups->isEmpty())
                <x-site.empty message="هنوز تعرفه‌ای منتشر نشده است." :href="route('home')" />
            @endif
        </div>

        <p class="mt-8 text-xs leading-6 text-muted">
            ارتقا به سطح بالاتر هر زمان ممکن است و مبلغ باقی‌مانده‌ی پلن فعلی از قیمت کسر می‌شود؛ بازگشت به سطح پایین‌تر امکان‌پذیر نیست.
        </p>
    </div>
    @if ($packages->isNotEmpty())
        <section id="sponsorship" class="mx-auto mt-20 max-w-7xl scroll-mt-24 px-4 sm:px-6 lg:px-8" aria-labelledby="sponsorship-heading">
            <div class="overflow-hidden rounded-3xl bg-navy-900 p-6 text-white sm:p-10">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold tracking-wide text-accent-soft">ویژه شرکت‌ها و مجموعه‌ها</p>
                        <h2 id="sponsorship-heading" class="mt-2 text-2xl font-extrabold">حامیان کوهستان</h2>
                        <p class="mt-3 text-sm leading-8 text-white/70">
                            با حمایت مالی از صندوق، در توسعه گردشگری و تربیت مربی سهیم شوید. لوگو و معرفی مجموعه‌ی شما در صفحه اصلی و صفحه حامیان نمایش داده می‌شود
                            و مبلغ حمایت، دست‌کم برابر حداقل بسته‌ی انتخابی است؛ مبلغ بالاتر هم هر زمان ممکن است.
                            حمایت شما صرف طرح‌های مشخصی می‌شود:
                        </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (\App\Enums\ProjectCategory::cases() as $projectCategory)
                                <a href="{{ route('projects.index', ['category' => $projectCategory->value]) }}" class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/85 transition hover:bg-white hover:text-navy-900">{{ $projectCategory->label() }}</a>
                            @endforeach
                        </div>
                    </div>

                    @if ($sponsorCount > 0)
                        <a href="{{ route('sponsors.index') }}" class="text-sm text-white/70 transition hover:text-white">
                            {{ fa_num($sponsorCount) }} حامی
                            @if ($sponsorSupport > 0)· {{ fa_num($sponsorSupport) }} تومان حمایت @endif
                            <span aria-hidden="true">←</span>
                        </a>
                    @endif
                </div>

                <div class="mt-8 grid gap-4 grid-cols-[repeat(auto-fit,minmax(16rem,1fr))]">
                    @foreach ($packages as $package)
                        <div class="flex flex-col rounded-2xl bg-white/5 p-6 ring-1 ring-white/15 transition hover:bg-white/10">
                            <h3 class="font-bold">{{ $package->name }}</h3>
                            <p class="mt-3 flex items-baseline gap-1.5">
                                <span class="text-xs text-white/60">از</span>
                                <span class="text-2xl font-extrabold tabular-nums">{{ fa_num($package->min_amount) }}</span>
                                <span class="text-xs text-white/60">تومان</span>
                            </p>
                            @if ($package->description)
                                <p class="mt-2 text-sm leading-7 text-white/65">{{ $package->description }}</p>
                            @endif
                            <ul class="mt-4 flex-1 space-y-2 border-t border-white/10 pt-4 text-sm text-white/85">
                                @foreach ($package->benefits ?? [] as $benefit)
                                    <li class="flex items-start gap-2.5">
                                        <x-site.icon name="check" class="mt-1 h-3.5 w-3.5 shrink-0 text-accent-soft" />
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <x-site.button :href="auth()->check() ? route('panel.sponsorship') : route('login')" variant="primary">
                        {{ auth()->check() ? 'ثبت حمایت در پنل من' : 'ورود و ثبت حمایت' }}
                    </x-site.button>
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>
