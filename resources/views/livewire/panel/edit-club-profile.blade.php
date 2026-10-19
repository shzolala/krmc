<div class="flex flex-col gap-6">
    <x-admin.page-header title="ویرایش باشگاه" subtitle="اطلاعاتی که در صفحه عمومی باشگاه نمایش داده می‌شود" icon="building-office-2" :back="route('panel.club.dashboard', $club)" back-label="داشبورد" />

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col gap-6 lg:col-span-2">
            <x-admin.card title="اطلاعات اصلی">
                <div class="space-y-5">
                    <flux:input wire:model="name" label="نام باشگاه" />
                    <flux:textarea wire:model="summary" label="خلاصه" rows="3" description="یک معرفی کوتاه (حداکثر ۵۰۰ نویسه)" />
                    <flux:textarea wire:model="description" label="توضیحات کامل" rows="8" />
                </div>
            </x-admin.card>
            <x-admin.card title="موقعیت">
                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input wire:model="city" label="شهر" />
                    <flux:input wire:model="province" label="استان" />
                    <flux:input wire:model="address" label="نشانی" class="sm:col-span-2" />
                </div>
            </x-admin.card>
            <x-admin.card title="مدیریت باشگاه">
                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input wire:model="manager" label="مدیر باشگاه" />
                    <flux:input wire:model="education_head" label="مسئول آموزش" />
                    <flux:input wire:model="founded_year" label="سال تأسیس (شمسی)" type="number" dir="ltr" />
                </div>
            </x-admin.card>
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="check">ذخیره باشگاه</flux:button>
            </div>
        </div>
        <div class="flex flex-col gap-6">
            <x-admin.card title="وضعیت در سایت">
                <div class="flex flex-wrap gap-2">
                    @if ($club?->is_verified) <flux:badge color="green">تأیید شده</flux:badge> @else <flux:badge color="amber">در انتظار تأیید</flux:badge> @endif
                    @if ($club?->is_featured) <flux:badge color="blue">ویژه</flux:badge> @endif
                    @if ($club?->is_premium) <flux:badge color="purple">پریمیوم</flux:badge> @endif
                </div>
                <p class="mt-3 text-xs leading-6 text-zinc-500">تأیید هویت، نمایش ویژه و عضویت پریمیوم توسط مدیریت سایت تعیین می‌شود.</p>
                @if ($club)
                    <flux:button class="mt-4 w-full" icon="arrow-top-right-on-square" href="{{ route('clubs.show', $club) }}" target="_blank">مشاهده صفحه عمومی</flux:button>
                @endif
            </x-admin.card>
            <x-admin.card title="نکته">
                <p class="text-sm leading-7 text-zinc-600 dark:text-zinc-400">توضیحات کامل و دقیق باعث می‌شود کوهنوردان راحت‌تر شما را پیدا کنند و به شما اعتماد کنند.</p>
            </x-admin.card>
        </div>
    </form>
</div>
