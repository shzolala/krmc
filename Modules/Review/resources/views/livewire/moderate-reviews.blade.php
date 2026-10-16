<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">مدیریت نظرات</flux:heading>
            <flux:subheading>نظرات ثبت‌شده را تأیید یا رد کنید.</flux:subheading>
        </div>
        <flux:select wire:model.live="filter" class="max-w-xs">
            <option value="pending">در انتظار</option>
            <option value="approved">تأیید شده</option>
            <option value="rejected">رد شده</option>
            <option value="all">همه</option>
        </flux:select>
    </div>

    <div class="space-y-4">
        @forelse ($reviews as $review)
            <flux:card class="space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <flux:heading size="sm">{{ $review->user?->name }}</flux:heading>
                        <flux:text class="text-sm">
                            {{ $review->reviewable_type }} #{{ $review->reviewable_id }}
                            · امتیاز {{ $review->rating }}
                            · {{ $review->status->label() }}
                        </flux:text>
                    </div>
                    @if ($review->isPending())
                        <div class="flex gap-2">
                            <flux:button size="sm" variant="primary" wire:click="approve({{ $review->id }})">تأیید</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="reject({{ $review->id }})">رد</flux:button>
                        </div>
                    @endif
                </div>
                @if ($review->title)
                    <flux:heading size="sm">{{ $review->title }}</flux:heading>
                @endif
                <flux:text>{{ $review->body }}</flux:text>
            </flux:card>
        @empty
            <flux:card>
                <flux:text>نظری برای نمایش وجود ندارد.</flux:text>
            </flux:card>
        @endforelse
    </div>

    {{ $reviews->links() }}
</div>
