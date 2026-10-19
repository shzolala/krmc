@props(['project'])

@php
    $sponsors = $project->incomes->pluck('sponsor')->filter(fn ($s) => $s?->status->value === 'active')->unique('id')->values();
    $isActive = $project->status->value === 'active';
@endphp
<a href="{{ route('projects.show', $project) }}" class="group flex h-full flex-col rounded-3xl bg-white p-5 ring-1 ring-navy-900/10 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent sm:p-6">
    <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-accent transition group-hover:bg-accent group-hover:text-navy-950">
            <x-site.icon :name="$project->category->icon()" class="h-5 w-5" />
        </span>
        <span class="min-w-0 flex-1 truncate text-xs font-semibold text-navy-700">{{ $project->category->label() }}</span>
        @unless ($isActive)
            <span class="shrink-0 rounded-full bg-navy-900/5 px-2.5 py-1 text-[11px] font-semibold text-muted">{{ $project->status->label() }}</span>
        @endunless
    </div>

    <h3 class="mt-4 text-base font-extrabold leading-7 text-navy-900 sm:text-lg">{{ $project->title }}</h3>
    @if ($project->summary)
        <p class="mt-1.5 line-clamp-2 text-sm leading-7 text-muted">{{ $project->summary }}</p>
    @endif

    <div class="mt-auto pt-5">
        @if ($project->budget > 0)
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-lg font-extrabold tabular-nums leading-none text-navy-900">
                        {{ fa_num($project->raisedTotal()) }} <span class="text-xs font-normal text-muted">تومان</span>
                    </p>
                    <p class="mt-1.5 text-xs text-muted">از بودجه‌ی {{ fa_num($project->budget) }}</p>
                </div>
                <span class="text-sm font-bold tabular-nums text-navy-800">{{ fa_num($project->progressPercent()) }}٪</span>
            </div>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-navy-900/10" role="progressbar" aria-valuenow="{{ $project->progressPercent() }}" aria-valuemin="0" aria-valuemax="100" aria-label="میزان جذب مالی">
                <div class="h-full rounded-full bg-accent" style="width: {{ $project->progressPercent() }}%"></div>
            </div>
        @endif

        @if ($sponsors->isNotEmpty())
            <div class="mt-4 flex items-center gap-2.5 border-t border-navy-900/10 pt-4">
                <div class="flex -space-x-2 space-x-reverse">
                    @foreach ($sponsors->take(4) as $sponsor)
                        <span class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-sand ring-2 ring-white" title="{{ $sponsor->name }}">
                            @if ($sponsor->logo_url)
                                <img src="{{ $sponsor->logo_url }}" alt="" class="h-4 w-4 object-contain">
                            @else
                                <span class="text-[10px] font-bold text-navy-900">{{ mb_substr($sponsor->name, 0, 1) }}</span>
                            @endif
                        </span>
                    @endforeach
                </div>
                <span class="text-xs text-muted">{{ fa_num($sponsors->count()) }} حامی</span>
            </div>
        @endif
    </div>
</a>
