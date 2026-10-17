@props([
    'coach',
])

@php
    $specialty = \Illuminate\Support\Str::of($coach->specialty ?? '')->replaceMatches('/^مربیگری\s*/u', '')->trim()->toString();
@endphp

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl bg-white text-center ring-1 ring-navy-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/10']) }}>
    <div class="relative h-24 overflow-hidden bg-navy-900">
        <img src="{{ $coach->club?->cover_path ?? asset('images/covers/cover-06.jpg') }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-60 transition duration-700 group-hover:scale-110">
        <div class="absolute inset-0 bg-linear-to-b from-navy-950/30 to-navy-900/80"></div>
        @if ($coach->is_premium)
            <span class="absolute left-3 top-3 rounded-full bg-accent px-2.5 py-0.5 text-[11px] font-bold text-navy-950">ویژه</span>
        @endif
        @if ($coach->is_leader)
            <span class="absolute right-3 top-3 rounded-full bg-white/90 px-2.5 py-0.5 text-[11px] font-bold text-navy-900">سرپرست</span>
        @endif
    </div>

    <div class="relative -mt-12 flex justify-center">
        <div class="relative">
            <x-site.avatar :name="$coach->user->name" :image="$coach->avatar_path" size="xl" class="ring-4 ring-white" />
            @if ($coach->is_verified)
                <span class="absolute -bottom-0.5 -left-0.5 flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500 text-white ring-2 ring-white" title="تأییدشده توسط فدراسیون">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-8 8a1 1 0 01-1.4 0l-4-4a1 1 0 011.4-1.4L8 12.6l7.3-7.3a1 1 0 011.4 0z" clip-rule="evenodd" /></svg>
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col px-5 pb-5 pt-3">
        <h3 class="text-lg font-extrabold text-navy-900">
            <a href="{{ route('coaches.show', $coach) }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:after:rounded-3xl focus-visible:after:ring-2 focus-visible:after:ring-accent">{{ $coach->user->name }}</a>
        </h3>
        <p class="mt-1 text-xs font-semibold text-accent">
            {{ $coach->roleLabel() }}@if ($specialty) · {{ $specialty }}@endif
        </p>
        @if ($coach->club)
            <p class="mt-2 line-clamp-1 text-xs text-muted">{{ $coach->club->name }}</p>
        @endif

        <dl class="mt-5 grid grid-cols-2 divide-x divide-x-reverse divide-navy-900/10 rounded-2xl bg-sand py-3">
            <div>
                <dd class="text-lg font-extrabold text-navy-900">{{ fa_num($coach->experience_years ?? 0) }}</dd>
                <dt class="text-[11px] text-muted">سال سابقه</dt>
            </div>
            <div>
                <dd class="text-lg font-extrabold text-navy-900">{{ fa_num($coach->total_courses_taught ?? 0) }}</dd>
                <dt class="text-[11px] text-muted">دوره برگزارشده</dt>
            </div>
        </dl>

        <p class="mt-auto inline-flex items-center justify-center gap-1.5 pt-4 text-xs font-bold text-navy-900 transition group-hover:text-accent">
            مشاهده پروفایل
            <span aria-hidden="true" class="transition group-hover:-translate-x-1">←</span>
        </p>
    </div>
</article>
