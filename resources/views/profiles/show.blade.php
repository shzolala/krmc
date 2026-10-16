<x-layouts.site :title="$profileUser->name">
    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-navy-900/5 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-muted" dir="ltr">{{ '@'.$profileUser->username }}</p>
                    <h1 class="mt-2 text-3xl font-extrabold text-navy-900">{{ $profileUser->name }}</h1>
                    <p class="mt-2 text-sm text-muted">{{ $profileUser->roleLabel() }}</p>
                </div>
                <span class="rounded-full bg-navy-900/5 px-3 py-1.5 text-xs font-semibold text-navy-900">
                    پروفایل عمومی
                </span>
            </div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-sand px-4 py-3">
                    <dt class="text-xs text-muted">یوزرنیم</dt>
                    <dd class="mt-1 font-medium" dir="ltr">{{ '@'.$profileUser->username }}</dd>
                </div>
                <div class="rounded-xl bg-sand px-4 py-3">
                    <dt class="text-xs text-muted">نقش</dt>
                    <dd class="mt-1 font-medium">{{ $profileUser->roleLabel() }}</dd>
                </div>
            </dl>

            <div class="mt-8 space-y-4">
                @if ($profileUser->coachProfile)
                    <div class="rounded-xl border border-navy-900/10 p-4">
                        <h2 class="text-sm font-bold text-navy-900">پروفایل تخصصی</h2>
                        <p class="mt-2 text-sm text-muted">{{ $profileUser->coachProfile->headline }}</p>
                        <p class="mt-1 text-sm">تخصص: {{ $profileUser->coachProfile->specialty }}</p>
                        <a href="{{ route('coaches.show', $profileUser->coachProfile) }}" class="mt-3 inline-block text-sm font-semibold text-accent hover:text-navy-900">
                            مشاهده صفحه مربی/سرپرست
                        </a>
                    </div>
                @endif

                @if ($profileUser->ownedClubs->isNotEmpty())
                    <div class="rounded-xl border border-navy-900/10 p-4">
                        <h2 class="text-sm font-bold text-navy-900">باشگاه‌ها</h2>
                        <ul class="mt-3 space-y-2">
                            @foreach ($profileUser->ownedClubs as $club)
                                <li>
                                    <a href="{{ route('clubs.show', $club) }}" class="text-sm font-semibold text-accent hover:text-navy-900">
                                        {{ $club->name }}
                                    </a>
                                    <p class="text-xs text-muted">{{ $club->summary }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($profileUser->shop)
                    <div class="rounded-xl border border-navy-900/10 p-4">
                        <h2 class="text-sm font-bold text-navy-900">فروشگاه</h2>
                        <p class="mt-2 text-sm text-muted">{{ $profileUser->shop->summary }}</p>
                        <a href="{{ route('shops.show', $profileUser->shop) }}" class="mt-3 inline-block text-sm font-semibold text-accent hover:text-navy-900">
                            مشاهده {{ $profileUser->shop->name }}
                        </a>
                    </div>
                @endif

                @if ($profileUser->primaryClub && $profileUser->ownedClubs->isEmpty())
                    <div class="rounded-xl border border-navy-900/10 p-4">
                        <h2 class="text-sm font-bold text-navy-900">باشگاه اصلی</h2>
                        <a href="{{ route('clubs.show', $profileUser->primaryClub) }}" class="mt-2 inline-block text-sm font-semibold text-accent hover:text-navy-900">
                            {{ $profileUser->primaryClub->name }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.site>
