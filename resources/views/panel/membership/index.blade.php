@php
    use App\Models\MembershipPlan;

    $defaultTerm = $termOptions->contains(12) ? 12 : (int) $termOptions->first();
@endphp

<x-layouts::app title="پلن‌های عضویت">
    <x-admin.page-header
        title="پلن‌های عضویت"
        subtitle="سطح و مدت مناسب خود را انتخاب کنید"
        icon="sparkles"
        :back="route('panel.finance')"
        back-label="صندوق و امور مالی"
    />

    <x-admin.card>
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm">
            @if ($activeDue)
                <span class="flex items-center gap-2">
                    پلن فعال:
                    <x-membership-badge :plan="$activeDue->plan" />
                    <span class="text-xs text-zinc-500">{{ fa_num($activeDue->remainingDays()) }} روز تا پایان اعتبار ({{ fa_date($activeDue->ends_on) }})</span>
                </span>
            @elseif ($openDue)
                <span class="text-xs text-zinc-600 dark:text-zinc-400">
                    انتخاب شما: «{{ $openDue->plan?->name ?? $openDue->title }}» — منتظر پرداخت ({{ fa_money($openDue->remainingAmount()) }} باقی‌مانده)
                </span>
            @else
                <span class="text-xs text-zinc-500">هنوز پلنی انتخاب نکرده‌اید.</span>
            @endif
            <span class="text-xs text-zinc-500">نشان سطح فقط برای اعضای واردشده دیده می‌شود.</span>
        </div>
        @if ($floorLevel > 0)
            <p class="mt-2 text-xs text-zinc-500">
                سطح ثبت‌شده شما «{{ fa_num($floorLevel) }}» است؛ سطح عضویت فقط می‌تواند ارتقا یابد و بازگشت به سطح پایین‌تر ممکن نیست.
            </p>
        @endif
    </x-admin.card>

    @if ($plans->isEmpty())
        <x-admin.card>
            <p class="text-sm text-zinc-500">هنوز پلنی برای گروه شما تعریف نشده است.</p>
        </x-admin.card>
    @else
        <div x-data="{ term: {{ $defaultTerm }} }" class="flex flex-col gap-5">
            <div role="radiogroup" aria-label="مدت عضویت" class="inline-flex w-fit flex-wrap gap-1 rounded-xl bg-zinc-200/70 p-1 dark:bg-zinc-800">
                @foreach ($termOptions as $months)
                    <button type="button" role="radio" :aria-checked="term === {{ $months }}" x-on:click="term = {{ $months }}"
                        :class="term === {{ $months }} ? 'bg-white shadow-sm dark:bg-zinc-950' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400'"
                        class="rounded-lg px-4 py-1.5 text-sm font-medium transition focus-visible:outline-2 focus-visible:outline-brand">
                        {{ MembershipPlan::TERM_LABELS[$months] }}
                    </button>
                @endforeach
            </div>

            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($plans as $plan)
                    @php
                        $evaluation = $evaluations[$plan->id];
                        $state = $evaluation['state'];
                        $offeredTerms = $plan->offeredTerms();
                        $offered = json_encode($offeredTerms);
                        $highlight = in_array($state, ['current', 'selected'], true);
                        $muted = $state === 'blocked';
                    @endphp
                    <section
                        aria-labelledby="plan-{{ $plan->id }}"
                        @class([
                            'flex flex-col rounded-xl border bg-white p-5 dark:bg-zinc-900',
                            'border-zinc-200 dark:border-zinc-800' => ! $highlight && ! $muted,
                            'border-navy-700 ring-1 ring-navy-700 dark:border-brand dark:ring-brand' => $highlight,
                            'border-dashed border-zinc-300 bg-transparent dark:border-zinc-700 dark:bg-transparent' => $muted,
                        ])
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div @class(['opacity-60' => $muted])>
                                <h2 id="plan-{{ $plan->id }}" class="text-base font-bold">{{ $plan->name }}</h2>
                                <p class="mt-0.5 text-xs text-zinc-500">سطح {{ fa_num($plan->level) }}</p>
                            </div>
                            <x-membership-badge :plan="$plan" :class="$muted ? 'opacity-60' : ''" />
                        </div>

                        @foreach ($termOptions as $months)
                            @php
                                $term = $plan->termPrice($months);
                                $isOffered = in_array($months, $offeredTerms, true);
                                $net = $state === 'upgrade' && $isOffered ? max(0, $term['final'] - $credit) : $term['final'];
                            @endphp
                            <div x-show="term === {{ $months }}" @unless ($months === $defaultTerm) style="display: none" @endunless class="mt-5">
                                <div class="flex items-baseline justify-between gap-3">
                                    <p @class(['flex items-baseline gap-1.5', 'opacity-40' => ! $isOffered])>
                                        <span @class(['text-2xl font-extrabold tabular-nums tracking-tight', 'line-through decoration-1' => ! $isOffered])>{{ fa_num($net) }}</span>
                                        <span class="text-xs text-zinc-500">تومان</span>
                                    </p>
                                    @if (! $isOffered)
                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-500 dark:bg-zinc-800">
                                            <flux:icon.lock-closed variant="mini" class="size-3" /> غیرفعال
                                        </span>
                                    @elseif ($term['discount'] > 0)
                                        <span class="shrink-0 rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-semibold dark:bg-zinc-800">{{ fa_num($term['discount']) }}٪ تخفیف</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">
                                    {{ $term['label'] }} · معادل ماهانه {{ fa_num($term['per_month']) }}
                                    @if ($state === 'upgrade' && $credit > 0 && $isOffered)
                                        · پس از کسر {{ fa_money($credit) }} اعتبار
                                    @endif
                                </p>
                            </div>
                        @endforeach

                        @if ($plan->description)
                            <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $plan->description }}</p>
                        @endif

                        @if ($plan->features)
                            <ul @class(['mt-4 flex-1 space-y-2 border-t border-zinc-100 pt-4 text-sm dark:border-zinc-800', 'text-zinc-500' => $muted])>
                                @foreach ($plan->features as $feature)
                                    <li class="flex items-start gap-2">
                                        <flux:icon.check variant="mini" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        <div class="mt-4">
                            @if ($evaluation['message'])
                                <p class="mb-2 text-xs text-zinc-500">{{ $evaluation['message'] }}</p>
                            @endif

                            @if ($state === 'current')
                                <flux:badge size="sm" color="green" icon="check">پلن فعال شما</flux:badge>
                            @elseif (! $muted)
                                <form method="POST" action="{{ route('panel.membership.choose', $plan) }}">
                                    @csrf
                                    <input type="hidden" name="months" :value="term">
                                    <flux:button type="submit" size="sm" variant="ghost" icon-trailing="arrow-left" class="-ms-2"
                                        x-bind:disabled="! {{ $offered }}.includes(term)">
                                        {{ match ($state) { 'upgrade' => 'ارتقا به این پلن', 'selected' => 'تغییر مدت', default => 'انتخاب این پلن' } }}
                                    </flux:button>
                                </form>
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>
        </div>

        <p class="text-xs text-zinc-500">پرداخت توسط مدیر صندوق ثبت می‌شود و پس از تسویه کامل، نشان سطح فعال می‌گردد. تا قبل از اولین پرداخت می‌توانید پلن و مدت را آزادانه تغییر دهید؛ پس از آن فقط ارتقا به سطح بالاتر ممکن است.</p>
    @endif
</x-layouts::app>
