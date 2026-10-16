<x-layouts.site :title="$coach->user->name">
    <section class="bg-navy-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm text-white/60">خانه / مربیان / {{ $coach->user->name }}</p>
            <div class="mt-4 flex flex-wrap gap-1.5">
                <x-badge tone="leader">{{ $coach->roleLabel() }}</x-badge>
                @if ($coach->is_verified)<x-badge tone="verified">تأییدشده</x-badge>@endif
                @if ($coach->is_premium)<x-badge tone="premium">ویژه</x-badge>@endif
            </div>
            <h1 class="mt-4 text-4xl font-extrabold">{{ $coach->user->name }}</h1>
            <p class="mt-3 text-sm text-white/75">{{ $coach->headline }}</p>
            @if ($coach->club)
                <p class="mt-4 text-sm">باشگاه: <a href="{{ route('clubs.show', $coach->club) }}" class="text-accent-soft hover:underline">{{ $coach->club->name }}</a></p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.3fr_0.9fr]">
            <div>
                <h2 class="text-xl font-bold text-navy-900">درباره</h2>
                <p class="mt-4 text-sm leading-8 text-muted">{{ $coach->bio }}</p>
                <p class="mt-4 text-sm text-muted">تخصص: {{ $coach->specialty }} · سابقه: {{ $coach->experience_years }} سال</p>
            </div>
            <aside>
                <h3 class="font-bold text-navy-900">مقاصدی که می‌برد</h3>
                <ul class="mt-4 space-y-3">
                    @forelse ($coach->destinationGuides as $guide)
                        <li class="rounded-xl bg-white p-4 ring-1 ring-navy-900/5">
                            <a href="{{ route('destinations.show', $guide->destination) }}" class="font-semibold text-navy-900 hover:text-accent">{{ $guide->destination->name }}</a>
                        </li>
                    @empty
                        <li class="text-sm text-muted">هنوز مقصدی ثبت نشده است.</li>
                    @endforelse
                </ul>
            </aside>
        </div>

        <div class="mt-10">
            <livewire:review.section :reviewable="$coach" :key="'coach-reviews-'.$coach->id" />
        </div>
    </section>
</x-layouts.site>
