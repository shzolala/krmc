<x-layouts.site :title="$sponsor->name" :meta-description="$sponsor->summary ?? 'معرفی حامی کوهستان'">
    <header class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">
        <x-site.breadcrumbs tone="dark" :items="['حامیان کوهستان' => route('sponsors.index'), $sponsor->name => null]" />
    </header>

    <x-site.detail-layout class="pt-6">
        <div class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10 sm:p-8">
            <div class="flex flex-wrap items-center gap-5">
                <div class="flex h-24 w-40 shrink-0 items-center justify-center rounded-2xl bg-sand px-5">
                    @if ($sponsor->logo_url)
                        <img src="{{ $sponsor->logo_url }}" alt="لوگوی {{ $sponsor->name }}" class="max-h-16 w-auto max-w-full object-contain">
                    @else
                        <span class="text-center text-sm font-bold text-navy-900/70">{{ $sponsor->name }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-accent/15 px-3 py-1 text-xs font-bold text-amber-800">
                        <x-site.icon name="star" class="h-3.5 w-3.5" /> حامی کوهستان
                    </span>
                    <h1 class="mt-2 text-2xl font-extrabold text-navy-900">{{ $sponsor->name }}</h1>
                    @if ($sponsor->summary)
                        <p class="mt-1.5 text-sm leading-7 text-muted">{{ $sponsor->summary }}</p>
                    @endif
                </div>
            </div>

            @if ($sponsor->description)
                <div class="mt-8 border-t border-navy-900/10 pt-6">
                    <h2 class="text-sm font-bold text-navy-900">درباره‌ی مجموعه</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-8 text-ink">{{ $sponsor->description }}</p>
                </div>
            @endif

            @if ($projects->isNotEmpty())
                <div class="mt-6 border-t border-navy-900/10 pt-6">
                    <h2 class="text-sm font-bold text-navy-900">طرح‌هایی که حمایت کرده است</h2>
                    <ul class="mt-3 space-y-4">
                        @foreach ($projects as $row)
                            <li>
                                <a href="{{ route('projects.show', $row['project']) }}" class="flex flex-wrap items-center justify-between gap-2 text-sm font-semibold text-navy-900 hover:text-accent">
                                    <span class="inline-flex items-center gap-2">
                                        <x-site.icon :name="$row['project']->category->icon()" class="h-4 w-4 text-accent" />
                                        {{ $row['project']->title }}
                                    </span>
                                    @if ($sponsor->show_amount)
                                        <span class="text-xs font-normal tabular-nums text-muted">{{ fa_money($row['total']) }}</span>
                                    @endif
                                </a>
                                <ul class="mt-1.5 space-y-1 text-xs text-muted">
                                    @foreach ($row['contributions'] as $income)
                                        <li>
                                            {{ $income->installment_no ? 'نوبت '.fa_num($income->installment_no) : fa_date($income->received_at) }}
                                            · {{ $income->is_in_kind ? 'کالا/خدمات: '.$income->in_kind_description : 'نقدی' }}
                                            @if ($sponsor->show_amount) · {{ fa_money($income->amount) }}@endif
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($sponsor->support_note)
                <div class="mt-6 border-t border-navy-900/10 pt-6">
                    <h2 class="text-sm font-bold text-navy-900">حمایت از کوهستان</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-8 text-ink">{{ $sponsor->support_note }}</p>
                </div>
            @endif
        </div>

        <x-slot:aside>
            <div class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10">
                <h2 class="text-sm font-bold text-navy-900">مجموع حمایت</h2>
                @if ($sponsor->show_amount && $sponsor->incomes_sum_amount > 0)
                    <p class="mt-2 text-3xl font-extrabold tabular-nums text-navy-900">{{ fa_num($sponsor->incomes_sum_amount) }}</p>
                    <p class="text-xs text-muted">تومان پرداخت‌شده به صندوق</p>
                @else
                    <p class="mt-2 text-sm text-muted">این حامی مبلغ حمایت را نمایش نمی‌دهد.</p>
                @endif

                <dl class="mt-5 space-y-2 border-t border-navy-900/10 pt-4 text-xs">
                    @if ($sponsor->package)
                        <div class="flex justify-between gap-2"><dt class="text-muted">بسته حمایت</dt><dd class="font-semibold text-navy-900">{{ $sponsor->package->name }}</dd></div>
                    @endif
                    @if ($sponsor->started_at)
                        <div class="flex justify-between gap-2"><dt class="text-muted">آغاز حمایت</dt><dd class="font-semibold text-navy-900">{{ fa_date($sponsor->started_at) }}</dd></div>
                    @endif
                </dl>

                @if ($sponsor->website)
                    <a href="{{ $sponsor->website }}" target="_blank" rel="sponsored noopener noreferrer"
                        class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-navy-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                        وب‌سایت {{ $sponsor->name }}
                        <x-site.icon name="arrow-up-left" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </x-slot:aside>
    </x-site.detail-layout>

    @if ($others->isNotEmpty())
        <x-site.section spacing="pt-14" title="سایر حامیان" :href="route('sponsors.index')" link-label="همه حامیان">
            <x-site.sponsor-marquee :sponsors="$others" :rows="1" />
        </x-site.section>
    @endif
</x-layouts.site>
