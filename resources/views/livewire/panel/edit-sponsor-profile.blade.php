<div class="flex flex-col gap-6">
    <x-admin.page-header title="حمایت از کوهستان" subtitle="معرفی مجموعه شما و مبلغ حمایت" icon="building-library" :back="route(auth()->user()->panelHomeRoute())" back-label="داشبورد" />

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-admin.card title="معرفی مجموعه" description="این اطلاعات در صفحه حامیان نمایش داده می‌شود">
                <div class="space-y-5">
                    <flux:input wire:model="name" label="نام شرکت یا مجموعه" />
                    <flux:textarea wire:model="summary" label="معرفی کوتاه" rows="3" description="در کارت حامیان نمایش داده می‌شود." />
                    <flux:textarea wire:model="description" label="معرفی کامل" rows="5" />
                    <flux:textarea wire:model="supportNote" label="حمایتی که انجام می‌دهید" rows="3" description="مثلاً تأمین هزینه دوره‌های مربی‌پروری." />
                    <flux:input wire:model="website" label="وب‌سایت" type="url" dir="ltr" placeholder="https://" />
                    <flux:input wire:model="logo" label="لوگو" type="file" accept="image/*" description="PNG، SVG یا JPG با پس‌زمینه شفاف یا سفید؛ حداکثر ۲ مگابایت." />
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" alt="پیش‌نمایش لوگو" class="h-14 w-auto rounded bg-zinc-50 p-2">
                    @elseif ($sponsor?->logo_url)
                        <img src="{{ $sponsor->logo_url }}" alt="لوگوی فعلی" class="h-14 w-auto rounded bg-zinc-50 p-2">
                    @endif
                </div>
            </x-admin.card>

            <x-admin.card title="هدف حمایت" description="مشخص کنید حمایت شما صرف چه کاری شود">
                <div class="space-y-5">
                    <flux:select wire:model.live="preferredCategory" label="دسته‌بندی">
                        <flux:select.option value="">فرقی ندارد (به صلاح صندوق)</flux:select.option>
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->value }}">{{ $category->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="projectId" label="طرح مشخص (اختیاری)">
                        <flux:select.option value="">همه‌ی طرح‌های این دسته</flux:select.option>
                        @foreach ($projects->when($preferredCategory !== '', fn ($list) => $list->where('category.value', $preferredCategory)) as $project)
                            <flux:select.option value="{{ $project->id }}">{{ $project->title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </x-admin.card>

            <x-admin.card title="مبلغ حمایت" :description="$minimum > 0 ? 'حداقل مبلغ حمایت '.fa_money($minimum).' است.' : null">
                <div class="space-y-5">
                    @if ($packages->isNotEmpty())
                        <flux:select wire:model.live="packageId" label="بسته حمایت">
                            <flux:select.option value="">بدون بسته (مبلغ دلخواه)</flux:select.option>
                            @foreach ($packages as $package)
                                <flux:select.option value="{{ $package->id }}">{{ $package->name }} — از {{ fa_money($package->min_amount) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif
                    <flux:input wire:model="pledgeAmount" label="مبلغ حمایت (تومان)" type="number" min="{{ $minimum }}" dir="ltr" />
                </div>
            </x-admin.card>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="check">ذخیره</flux:button>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <x-admin.card title="وضعیت">
                @if ($sponsor)
                    <flux:badge :color="$sponsor->status->value === 'active' ? 'green' : 'amber'">{{ $sponsor->status->label() }}</flux:badge>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-2"><dt class="text-zinc-500">مبلغ تعهد</dt><dd class="font-semibold">{{ fa_money($sponsor->pledge_amount) }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-zinc-500">پرداخت‌شده</dt><dd class="font-semibold">{{ fa_money($sponsor->incomes_sum_amount) }}</dd></div>
                    </dl>
                    @if ($sponsor->status->value === 'active')
                        <flux:button class="mt-4 w-full" icon="arrow-top-right-on-square" href="{{ route('sponsors.show', $sponsor) }}" target="_blank">مشاهده صفحه عمومی</flux:button>
                    @else
                        <p class="mt-3 text-xs leading-6 text-zinc-500">پس از تأیید مدیریت و ثبت پرداخت، لوگو و معرفی شما در سایت نمایش داده می‌شود.</p>
                    @endif
                @else
                    <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">هنوز حمایتی ثبت نکرده‌اید. اطلاعات را پر کنید و ذخیره کنید.</p>
                @endif
            </x-admin.card>
            <x-admin.card title="مزایا">
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">لوگو و معرفی مجموعه در صفحه اصلی و صفحه حامیان نمایش داده می‌شود و به وب‌سایت شما لینک دارد.</p>
            </x-admin.card>
        </div>
    </form>
</div>
