<div class="flex flex-col gap-6">
    <x-admin.page-header
        title="نظرات کاربران"
        subtitle="نظرات ثبت‌شده را بررسی، تأیید، رد یا حذف کنید."
        icon="chat-bubble-left-right"
        :back="route('panel.admin.dashboard')"
        back-label="داشبورد"
    />

    <div class="flex flex-wrap gap-2">
        @foreach ($statuses as $status)
            <button type="button" wire:click="$set('filter', '{{ $status->value }}')"
                @class([
                    'rounded-full border px-3.5 py-1.5 text-xs font-medium transition',
                    'border-navy-900 bg-navy-900 text-white dark:border-brand dark:bg-brand dark:text-zinc-900' => $filter === $status->value,
                    'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' => $filter !== $status->value,
                ])>
                {{ $status->label() }} ({{ fa_num($counts[$status->value] ?? 0) }})
            </button>
        @endforeach
        <button type="button" wire:click="$set('filter', 'all')"
            @class([
                'rounded-full border px-3.5 py-1.5 text-xs font-medium transition',
                'border-navy-900 bg-navy-900 text-white dark:border-brand dark:bg-brand dark:text-zinc-900' => $filter === 'all',
                'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' => $filter !== 'all',
            ])>
            همه ({{ fa_num($counts->sum()) }})
        </button>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <div class="min-w-56 flex-1">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="جستجو در نظرات یا نام کاربر…" />
        </div>
        <div class="w-44">
            <flux:select wire:model.live="rating">
                <option value="">همه امتیازها</option>
                @foreach ([5, 4, 3, 2, 1] as $stars)
                    <option value="{{ $stars }}">{{ fa_num($stars) }} ستاره</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <div class="flex flex-col gap-3">
        @forelse ($reviews as $review)
            @php $target = $this->describe($review); @endphp
            <x-admin.card wire:key="review-{{ $review->id }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <flux:avatar size="sm" :name="$review->user?->name" />
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold">{{ $review->user?->name ?? 'کاربر حذف‌شده' }}</span>
                                <span class="text-sm tracking-tight text-amber-500" title="امتیاز {{ fa_num($review->rating) }}">{{ str_repeat('★', $review->rating) }}<span class="text-zinc-300 dark:text-zinc-600">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                                <flux:badge size="sm" :color="match ($review->status->value) { 'approved' => 'green', 'rejected' => 'red', default => 'amber' }">
                                    {{ $review->status->label() }}
                                </flux:badge>
                            </div>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 text-xs text-zinc-500">
                                <span>{{ $target['type'] }}:</span>
                                @if ($target['admin_url'])
                                    <a href="{{ $target['admin_url'] }}" wire:navigate class="font-medium text-zinc-700 hover:text-brand dark:text-zinc-300">{{ $target['name'] }}</a>
                                @else
                                    <span class="font-medium">{{ $target['name'] }}</span>
                                @endif
                                @if ($target['public_url'])
                                    <a href="{{ $target['public_url'] }}" target="_blank" class="hover:text-brand" title="مشاهده در سایت"><flux:icon.arrow-top-right-on-square class="size-3.5" /></a>
                                @endif
                                <span>· {{ fa_date($review->created_at, true) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($review->status !== \Modules\Review\Enums\ReviewStatus::Approved)
                            <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $review->id }})">تأیید</flux:button>
                        @endif
                        @if ($review->status !== \Modules\Review\Enums\ReviewStatus::Rejected)
                            <flux:button size="sm" icon="x-mark" wire:click="reject({{ $review->id }})">رد</flux:button>
                        @endif
                        @if (! $review->isPending())
                            <flux:button size="sm" variant="ghost" icon="arrow-uturn-right" wire:click="reopen({{ $review->id }})" title="بازگرداندن به صف بررسی" />
                        @endif
                        <flux:button size="sm" variant="ghost" icon="trash" class="text-red-600!" title="حذف"
                            wire:click="delete({{ $review->id }})" wire:confirm="این نظر برای همیشه حذف شود؟" />
                    </div>
                </div>

                @if ($review->title)
                    <h3 class="mt-3 text-sm font-bold">{{ $review->title }}</h3>
                @endif
                <p @class(['text-sm leading-7 text-zinc-700 dark:text-zinc-300', 'mt-3' => ! $review->title, 'mt-1' => $review->title])>{{ $review->body }}</p>

                @if ($review->moderator && $review->moderated_at)
                    <p class="mt-3 text-xs text-zinc-500">بررسی‌شده توسط {{ $review->moderator->name }} در {{ fa_date($review->moderated_at, true) }}</p>
                @endif
            </x-admin.card>
        @empty
            <x-admin.card>
                <div class="py-10 text-center">
                    <flux:icon.chat-bubble-left-right class="mx-auto size-10 text-zinc-300" />
                    <p class="mt-3 text-sm text-zinc-500">نظری برای نمایش وجود ندارد.</p>
                </div>
            </x-admin.card>
        @endforelse
    </div>

    {{ $reviews->links() }}
</div>
