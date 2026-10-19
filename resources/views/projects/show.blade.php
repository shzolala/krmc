@php
    $raised = $project->raisedTotal();
    $remaining = max(0, $project->budget - $raised);
@endphp

<x-layouts.site :title="$project->title" :meta-description="$project->summary ?? $project->category->description()">
    <header class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">
        <x-site.breadcrumbs tone="dark" :items="['طرح‌های حمایتی' => route('projects.index'), $project->title => null]" />
    </header>

    <x-site.detail-layout class="pt-6">
        <div class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10 sm:p-8">
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <a href="{{ route('projects.index', ['category' => $project->category->value]) }}" class="inline-flex items-center gap-1.5 rounded-full bg-navy-900/5 px-3 py-1 font-semibold text-navy-800 transition hover:bg-navy-900 hover:text-white">
                    <x-site.icon :name="$project->category->icon()" class="h-3.5 w-3.5" /> {{ $project->category->label() }}
                </a>
                <span class="text-muted">{{ $project->status->label() }}</span>
            </div>
            <h1 class="mt-3 text-2xl font-extrabold text-navy-900">{{ $project->title }}</h1>
            @if ($project->summary)
                <p class="mt-2 text-sm leading-7 text-muted">{{ $project->summary }}</p>
            @endif
            @if ($project->description)
                <p class="mt-6 whitespace-pre-line border-t border-navy-900/10 pt-6 text-sm leading-8 text-ink">{{ $project->description }}</p>
            @endif
        </div>

        <section class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10 sm:p-8" aria-labelledby="activities-heading">
            <h2 id="activities-heading" class="text-base font-bold text-navy-900">اقدامات جامعه کوهنوردان</h2>
            @if ($project->activities->isEmpty())
                <p class="mt-3 text-sm text-muted">هنوز اقدامی برای این طرح ثبت نشده است.</p>
            @else
                <ol class="mt-5 space-y-5 border-s border-navy-900/15 ps-5">
                    @foreach ($project->activities as $activity)
                        <li class="relative">
                            <span class="absolute -start-[1.6rem] top-1.5 h-2.5 w-2.5 rounded-full bg-accent ring-4 ring-white" aria-hidden="true"></span>
                            @if ($activity->happened_on)
                                <p class="text-xs text-muted">{{ fa_date($activity->happened_on, false, true) }}</p>
                            @endif
                            <h3 class="text-sm font-bold text-navy-900">{{ $activity->title }}</h3>
                            @if ($activity->description)
                                <p class="mt-1 text-sm leading-7 text-muted">{{ $activity->description }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <section class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10 sm:p-8" aria-labelledby="supporters-heading">
            <h2 id="supporters-heading" class="text-base font-bold text-navy-900">حامیان این طرح</h2>
            @if ($supporters->isEmpty())
                <p class="mt-3 text-sm text-muted">هنوز حامی‌ای برای این طرح ثبت نشده است.
                    <a href="{{ route('pricing') }}#sponsorship" class="font-semibold text-navy-900 underline decoration-accent underline-offset-4">نخستین حامی باشید</a></p>
            @else
                <ul class="mt-5 divide-y divide-navy-900/10">
                    @foreach ($supporters as $row)
                        @php $sponsor = $row['sponsor']; @endphp
                        <li class="py-5 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('sponsors.show', $sponsor) }}" class="flex h-14 w-24 shrink-0 items-center justify-center rounded-xl bg-sand px-3">
                                    @if ($sponsor->logo_url)
                                        <img src="{{ $sponsor->logo_url }}" alt="لوگوی {{ $sponsor->name }}" class="max-h-9 w-auto max-w-full object-contain">
                                    @else
                                        <span class="text-center text-xs font-bold text-navy-900/70">{{ $sponsor->name }}</span>
                                    @endif
                                </a>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('sponsors.show', $sponsor) }}" class="font-bold text-navy-900 hover:text-accent">{{ $sponsor->name }}</a>
                                    @if ($sponsor->show_amount)
                                        <p class="text-xs text-muted">مجموع حمایت در این طرح: <strong class="tabular-nums text-navy-900">{{ fa_money($row['total']) }}</strong></p>
                                    @endif
                                </div>
                            </div>

                            <ul class="mt-3 space-y-1.5 ps-1 text-xs text-muted sm:ps-[7rem]">
                                @foreach ($row['contributions'] as $income)
                                    <li class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <span class="rounded-md bg-navy-900/5 px-2 py-0.5 font-semibold text-navy-800">
                                            {{ $income->installment_no ? 'نوبت '.fa_num($income->installment_no) : fa_date($income->received_at) }}
                                        </span>
                                        @if ($income->is_in_kind)
                                            <span>کالا/خدمات: <strong class="font-semibold text-navy-900">{{ $income->in_kind_description }}</strong></span>
                                            @if ($sponsor->show_amount)<span>· معادل {{ fa_money($income->amount) }}</span>@endif
                                        @else
                                            <span>حمایت نقدی@if ($sponsor->show_amount) · {{ fa_money($income->amount) }}@endif</span>
                                        @endif
                                        @if ($income->installment_no)<span>· {{ fa_date($income->received_at) }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <x-slot:aside>
            <div class="rounded-3xl bg-white p-6 ring-1 ring-navy-900/10">
                <h2 class="text-sm font-bold text-navy-900">وضعیت مالی طرح</h2>
                <p class="mt-3 text-3xl font-extrabold tabular-nums text-navy-900">{{ fa_num($raised) }}</p>
                <p class="text-xs text-muted">تومان جذب‌شده @if ($project->budget > 0) از بودجه‌ی {{ fa_num($project->budget) }} تومان @endif</p>

                @if ($project->budget > 0)
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-navy-900/10" role="progressbar" aria-valuenow="{{ $project->progressPercent() }}" aria-valuemin="0" aria-valuemax="100" aria-label="میزان جذب مالی">
                        <div class="h-full rounded-full bg-navy-800" style="width: {{ $project->progressPercent() }}%"></div>
                    </div>
                    <p class="mt-1.5 text-xs text-muted">{{ fa_num($project->progressPercent()) }}٪ از هدف</p>
                @endif

                <dl class="mt-5 space-y-2 border-t border-navy-900/10 pt-4 text-xs">
                    <div class="flex justify-between gap-2"><dt class="text-muted">حمایت نقدی</dt><dd class="font-semibold tabular-nums text-navy-900">{{ fa_money($project->cashRaised()) }}</dd></div>
                    @if ($project->inKindRaised() > 0)
                        <div class="flex justify-between gap-2"><dt class="text-muted">حمایت غیرنقدی (معادل)</dt><dd class="font-semibold tabular-nums text-navy-900">{{ fa_money($project->inKindRaised()) }}</dd></div>
                    @endif
                    @if ($project->spentTotal() > 0)
                        <div class="flex justify-between gap-2"><dt class="text-muted">هزینه‌شده</dt><dd class="font-semibold tabular-nums text-navy-900">{{ fa_money($project->spentTotal()) }}</dd></div>
                    @endif
                    @if ($project->budget > 0)
                        <div class="flex justify-between gap-2"><dt class="text-muted">مانده تا هدف</dt><dd class="font-semibold tabular-nums text-navy-900">{{ fa_money($remaining) }}</dd></div>
                    @endif
                    @if ($project->starts_on)
                        <div class="flex justify-between gap-2"><dt class="text-muted">شروع</dt><dd class="font-semibold text-navy-900">{{ fa_date($project->starts_on) }}</dd></div>
                    @endif
                </dl>

                <a href="{{ route('pricing') }}#sponsorship" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-full bg-navy-900 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-navy-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
                    حامی این طرح شوید
                </a>
            </div>
        </x-slot:aside>
    </x-site.detail-layout>
</x-layouts.site>
