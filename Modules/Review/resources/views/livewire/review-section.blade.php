<div id="reviews" class="scroll-mt-24 rounded-2xl bg-white p-6 ring-1 ring-navy-900/5">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-navy-900">نظرات و امتیازها</h2>
            <p class="mt-1 text-sm text-muted">پس از بررسی مدیر، نظر شما نمایش داده می‌شود.</p>
        </div>
        <div class="text-left">
            @if ($reviewsCount > 0)
                <p class="text-2xl font-extrabold text-navy-900">{{ fa_num($averageRating, 1) }}</p>
                <p class="text-xs text-muted">از ۵ · {{ fa_num($reviewsCount) }} نظر</p>
            @else
                <p class="text-sm text-muted">هنوز نظری تأیید نشده است.</p>
            @endif
        </div>
    </div>

    @if ($submitted)
        <div class="mt-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-900/10" role="status">
            نظر شما ثبت شد و پس از تأیید مدیر نمایش داده می‌شود.
        </div>
    @endif

    @auth
        @if ($userReview)
            <div class="mt-6 rounded-xl bg-navy-50 px-4 py-3 text-sm text-navy-900 ring-1 ring-navy-900/10">
                شما قبلاً برای این مورد نظر ثبت کرده‌اید
                ({{ $userReview->status->label() }}).
            </div>
        @elseif (! $submitted)
            <form wire:submit="submit" class="mt-6 space-y-4">
                <div>
                    <label for="review-rating" class="block text-sm font-semibold text-navy-900">امتیاز</label>
                    <select
                        id="review-rating"
                        wire:model="rating"
                        class="mt-2 w-full rounded-xl border-0 bg-navy-50 px-3 py-2.5 text-sm text-navy-900 ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent"
                    >
                        @for ($score = 5; $score >= 1; $score--)
                            <option value="{{ $score }}">{{ fa_num($score) }} از ۵</option>
                        @endfor
                    </select>
                    @error('rating')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="review-title" class="block text-sm font-semibold text-navy-900">عنوان (اختیاری)</label>
                    <input
                        id="review-title"
                        type="text"
                        wire:model="title"
                        maxlength="120"
                        class="mt-2 w-full rounded-xl border-0 bg-navy-50 px-3 py-2.5 text-sm text-navy-900 ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent"
                    >
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="review-body" class="block text-sm font-semibold text-navy-900">متن نظر</label>
                    <textarea
                        id="review-body"
                        wire:model="body"
                        rows="4"
                        class="mt-2 w-full rounded-xl border-0 bg-navy-50 px-3 py-2.5 text-sm text-navy-900 ring-1 ring-navy-900/10 focus:ring-2 focus:ring-accent"
                        placeholder="تجربه خود را بنویسید..."
                    ></textarea>
                    @error('body')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-accent px-4 py-2.5 text-sm font-semibold text-white hover:bg-accent/90"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">ثبت نظر</span>
                    <span wire:loading wire:target="submit">در حال ثبت...</span>
                </button>
            </form>
        @endif
    @else
        <p class="mt-6 text-sm text-muted">برای ثبت نظر باید وارد حساب کاربری شوید.</p>
    @endauth

    <div class="mt-8 space-y-4">
        @forelse ($approvedReviews as $review)
            <article class="rounded-xl bg-navy-50/60 p-4 ring-1 ring-navy-900/5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-navy-900">{{ $review->user->name }}</p>
                    <p class="text-xs font-bold text-accent">{{ fa_num($review->rating) }} / ۵</p>
                </div>
                @if ($review->title)
                    <h3 class="mt-2 text-sm font-bold text-navy-900">{{ $review->title }}</h3>
                @endif
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-muted">{{ $review->body }}</p>
                <p class="mt-2 text-[11px] text-muted">{{ fa_digits($review->created_at?->format('Y/m/d')) }}</p>
            </article>
        @empty
            <p class="text-sm text-muted">هنوز نظری برای نمایش وجود ندارد.</p>
        @endforelse
    </div>
</div>
