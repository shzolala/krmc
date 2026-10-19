@props([
    'summary',
    'campaigns',
    'memberDues' => null,
    'canViewLedger' => false,
    'expenses' => collect(),
    'incomes' => collect(),
    'supporters' => collect(),
])

<div @class(['grid gap-4 sm:grid-cols-3', 'xl:grid-cols-4' => $summary['inKind'] > 0])>
    <x-admin.stat label="موجودی صندوق" :value="fa_money($summary['balance'])" icon="banknotes" :tone="$summary['balance'] >= 0 ? 'green' : 'red'" />
    <x-admin.stat label="کل درآمد" :value="fa_money($summary['income'])" icon="arrow-down-tray" tone="blue" hint="حق عضویت، اسپانسر و کمک‌های مالی" />
    <x-admin.stat label="کل هزینه" :value="fa_money($summary['expense'])" icon="arrow-up-tray" tone="amber" hint="هزینه‌شده برای گسترش فعالیت‌ها" />
    @if ($summary['inKind'] > 0)
        <x-admin.stat label="حمایت غیرنقدی" :value="fa_money($summary['inKind'])" icon="gift" tone="purple" hint="معادل ریالی کالا و خدمات (تهاتر)؛ در موجودی نقدی نیست" />
    @endif
</div>

@if ($memberDues !== null)
    <x-admin.card title="وضعیت عضویت و پرداخت‌های من" description="سهم شما در صندوق مشترک انجمن">
        <x-slot:actions>
            <x-membership-badge :user="auth()->user()" />
            <a href="{{ route('panel.membership') }}" wire:navigate class="text-xs text-zinc-500 hover:text-brand">پلن‌های عضویت</a>
        </x-slot:actions>
        @if ($memberDues['dues']->isEmpty())
            <p class="text-sm text-zinc-500">هنوز تعهد حق عضویتی برای شما ثبت نشده است.</p>
        @else
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <span class="block text-xs text-zinc-500">پرداخت‌شده</span>
                    <span class="text-lg font-extrabold text-emerald-600">{{ fa_money($memberDues['paid']) }}</span>
                </div>
                <div>
                    <span class="block text-xs text-zinc-500">باقی‌مانده</span>
                    <span class="text-lg font-extrabold {{ $memberDues['remaining'] > 0 ? 'text-amber-600' : 'text-zinc-700 dark:text-zinc-200' }}">{{ fa_money($memberDues['remaining']) }}</span>
                </div>
                <div>
                    <span class="block text-xs text-zinc-500">کل تعهد</span>
                    <span class="text-lg font-extrabold">{{ fa_money($memberDues['total']) }}</span>
                </div>
            </div>
            <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" role="progressbar" aria-valuenow="{{ $memberDues['percent'] }}" aria-valuemin="0" aria-valuemax="100">
                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $memberDues['percent'] }}%"></div>
            </div>
            <p class="mt-1 text-xs text-zinc-500">{{ fa_num($memberDues['percent']) }}٪ از تعهد عضویت پرداخت شده است.</p>

            <ul class="mt-5 divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($memberDues['dues'] as $due)
                    <li class="flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                        <div class="min-w-0">
                            <span class="font-medium">دوره {{ fa_digits($due->period) }}</span>
                            @if ($due->plan)
                                <span class="text-xs text-zinc-500"> · {{ $due->plan->name }}</span>
                            @endif
                            @if ($due->due_date)
                                <span class="text-xs text-zinc-500"> · سررسید {{ fa_date($due->due_date) }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-zinc-500">{{ fa_money($due->paidAmount()) }} از {{ fa_money($due->amount) }}</span>
                            @if ($due->remainingAmount() === 0)
                                <flux:badge size="sm" color="green">تسویه</flux:badge>
                            @elseif ($due->isOverdue())
                                <flux:badge size="sm" color="red">معوق · {{ fa_money($due->remainingAmount()) }}</flux:badge>
                            @else
                                <flux:badge size="sm" color="amber">مانده {{ fa_money($due->remainingAmount()) }}</flux:badge>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-admin.card>
@endif

<div class="grid gap-6 md:grid-cols-2">
    <x-admin.card title="درآمد به تفکیک منبع">
        <ul class="space-y-3">
            @forelse ($summary['incomeBySource'] as $row)
                <li>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-medium">{{ $row['source']->label() }}</span>
                        <span class="text-zinc-500">{{ fa_money($row['total']) }} · {{ fa_num($row['percent']) }}٪</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div class="h-full rounded-full {{ $row['source']->color() }}" style="width: {{ $row['percent'] }}%"></div>
                    </div>
                </li>
            @empty
                <li class="text-sm text-zinc-500">هنوز درآمدی ثبت نشده است.</li>
            @endforelse
        </ul>
    </x-admin.card>

    <x-admin.card title="هزینه‌ها به تفکیک دسته‌بندی">
        <ul class="space-y-3">
            @forelse ($summary['expenseByCategory'] as $row)
                <li>
                    <div class="mb-1 flex items-center justify-between text-xs">
                        <span class="font-medium">{{ $row['category']->label() }}</span>
                        <span class="text-zinc-500">{{ fa_money($row['total']) }} · {{ fa_num($row['percent']) }}٪</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div class="h-full rounded-full {{ $row['category']->color() }}" style="width: {{ $row['percent'] }}%"></div>
                    </div>
                </li>
            @empty
                <li class="text-sm text-zinc-500">هنوز هزینه‌ای ثبت نشده است.</li>
            @endforelse
        </ul>
    </x-admin.card>
</div>

@if ($supporters->isNotEmpty())
    <x-admin.card title="حامیان صندوق" description="اعضایی که حق عضویت پلن خود را تسویه کرده‌اند">
        <ul class="flex flex-wrap gap-x-6 gap-y-3">
            @foreach ($supporters as $due)
                <li class="flex items-center gap-2 text-sm">
                    <span class="font-medium">{{ $due->user->name }}</span>
                    <x-membership-badge :plan="$due->plan" />
                </li>
            @endforeach
        </ul>
    </x-admin.card>
@endif

@if ($campaigns->isNotEmpty())
    <x-admin.card title="کمپین‌های جذب سرمایه و اسپانسر" description="جمع‌آوری منابع برای گسترش گردشگری و تربیت مربی">
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($campaigns as $campaign)
                <div class="rounded-lg border border-zinc-100 p-4 dark:border-zinc-800">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-sm font-bold">{{ $campaign->title }}</h3>
                        @unless ($campaign->is_active)
                            <flux:badge size="sm">پایان‌یافته</flux:badge>
                        @endunless
                    </div>
                    @if ($campaign->description)
                        <p class="mt-1 line-clamp-2 text-xs text-zinc-500">{{ $campaign->description }}</p>
                    @endif
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <div class="h-full rounded-full bg-violet-500" style="width: {{ $campaign->progressPercent() }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-zinc-500">
                        {{ fa_money($campaign->raisedAmount()) }} از {{ fa_money($campaign->goal_amount) }} ({{ fa_num($campaign->progressPercent()) }}٪)
                    </p>
                </div>
            @endforeach
        </div>
    </x-admin.card>
@endif

@if ($canViewLedger)
    <x-admin.card title="ریز هزینه‌ها" description="فقط برای گروه‌ها و افراد مجاز قابل مشاهده است" :padded="false">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">شرح</th>
                        <th class="px-4 py-3 text-start font-semibold">دسته‌بندی</th>
                        <th class="px-4 py-3 text-start font-semibold">مبلغ</th>
                        <th class="px-4 py-3 text-start font-semibold">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($expenses as $expense)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $expense->title }}</span>
                                @if ($expense->description)
                                    <div class="text-xs text-zinc-500">{{ $expense->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3"><flux:badge size="sm">{{ $expense->category->label() }}</flux:badge></td>
                            <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ fa_money($expense->amount) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs">{{ fa_date($expense->spent_at) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-zinc-500">هزینه‌ای ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <x-admin.card title="ریز دریافتی‌ها" :padded="false">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-xs text-zinc-500 dark:bg-zinc-800/50">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">پرداخت‌کننده</th>
                        <th class="px-4 py-3 text-start font-semibold">منبع</th>
                        <th class="px-4 py-3 text-start font-semibold">مبلغ</th>
                        <th class="px-4 py-3 text-start font-semibold">تاریخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($incomes as $income)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $income->payerLabel() }}</span>
                                @if ($income->is_in_kind)
                                    <div class="text-xs text-zinc-500">کالا/خدمات: {{ $income->in_kind_description }}</div>
                                @endif
                                @if ($income->project)
                                    <div class="text-xs text-zinc-500">طرح: {{ $income->project->title }}@if ($income->installment_no) · نوبت {{ fa_num($income->installment_no) }}@endif</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <flux:badge size="sm">{{ $income->source->label() }}</flux:badge>
                                @if ($income->is_in_kind) <flux:badge size="sm" color="purple">غیرنقدی</flux:badge> @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 tabular-nums">{{ fa_money($income->amount) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-xs">{{ fa_date($income->received_at) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-zinc-500">دریافتی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
@else
    <x-admin.card>
        <div class="flex items-center gap-3 text-sm text-zinc-500">
            <flux:icon.lock-closed class="size-5 shrink-0" />
            ریز دریافتی‌ها و هزینه‌ها فقط برای گروه‌ها و افراد مجاز نمایش داده می‌شود. خلاصه وضعیت مالی بالا برای همه اعضا در دسترس است.
        </div>
    </x-admin.card>
@endif
