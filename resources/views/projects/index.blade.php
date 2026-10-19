<x-layouts.site title="طرح‌های حمایتی" meta-description="طرح‌های حمایتی کوهنوردی کرمان؛ تربیت مربی، حفظ پناهگاه‌ها، محیط زیست و معرفی مکان‌های دیدنی با حمایت ارگان‌ها و شرکت‌ها.">
    <header class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8 lg:pt-8">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-2xl">
                <h1 class="text-2xl font-extrabold text-navy-900 sm:text-3xl">طرح‌های حمایتی</h1>
                <p class="mt-2 text-sm leading-7 text-muted">
                    هر طرح یک هدف مشخص دارد: از تربیت مربی تا حفظ پناهگاه‌ها و معرفی مکان‌های دیدنی.
                    اقدامات جامعه کوهنوردان، بودجه، میزان جذب مالی و حامیان هر طرح در صفحه‌ی آن دیده می‌شود.
                </p>
            </div>

            @if ($totalBudget > 0)
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-navy-900/10">
                    <p class="text-xs text-muted">جذب‌شده از بودجه‌ی طرح‌ها</p>
                    <p class="mt-0.5 text-xl font-extrabold tabular-nums text-navy-900">
                        {{ fa_num($totalRaised) }} <span class="text-xs font-normal text-muted">از {{ fa_num($totalBudget) }} تومان</span>
                    </p>
                </div>
            @endif
        </div>
    </header>

    <div class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav aria-label="دسته‌بندی طرح‌ها" class="flex gap-6 overflow-x-auto border-b border-navy-900/10">
            <a href="{{ route('projects.index') }}" @class(['-mb-px shrink-0 border-b-2 pb-3 text-sm font-semibold transition', 'border-navy-900 text-navy-900' => ! $category, 'border-transparent text-muted hover:text-navy-900' => $category]) @if (! $category) aria-current="page" @endif>همه</a>
            @foreach ($categories as $item)
                <a href="{{ route('projects.index', ['category' => $item->value]) }}" @class(['-mb-px shrink-0 border-b-2 pb-3 text-sm font-semibold transition', 'border-navy-900 text-navy-900' => $category === $item, 'border-transparent text-muted hover:text-navy-900' => $category !== $item]) @if ($category === $item) aria-current="page" @endif>
                    {{ $item->label() }}
                    @if (($counts[$item->value] ?? 0) > 0)<span class="text-xs font-normal text-muted">({{ fa_num($counts[$item->value]) }})</span>@endif
                </a>
            @endforeach
        </nav>
    </div>

    <x-site.section spacing="pt-8">
        @if ($category)
            <p class="mb-5 text-sm text-muted">{{ $category->description() }}</p>
        @endif

        @if ($projects->isEmpty())
            <x-site.empty message="طرحی در این دسته ثبت نشده است." :href="route('projects.index')" />
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-site.project-card :project="$project" />
                @endforeach
            </div>
        @endif
    </x-site.section>
</x-layouts.site>
