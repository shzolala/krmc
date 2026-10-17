<x-layouts.site :title="$attraction->name" :meta-description="$attraction->summary">
    <x-site.detail-header
        :title="$attraction->name"
        :crumbs="['جاذبه‌ها' => route('attractions.index'), $attraction->name => null]"
        :kicker="$attraction->typeLabel()"
        :cover="$attraction->cover_path"
        :seed="'attraction-'.$attraction->slug"
        :reviewable="$attraction"
    >
        <x-slot:badges>
            @if ($attraction->isUnesco())
                <span class="rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-bold text-white">ثبت یونسکو</span>
            @endif
        </x-slot:badges>
        <x-slot:meta>
            @if ($attraction->city)
                <span class="inline-flex items-center gap-1.5"><x-site.icon name="pin" class="h-4 w-4 text-accent" />{{ $attraction->city }}</span>
            @endif
        </x-slot:meta>
    </x-site.detail-header>

    <x-site.detail-layout>
        <x-site.panel title="درباره جاذبه" icon="star">
            <p class="whitespace-pre-line text-sm leading-8 text-muted">{{ $attraction->description }}</p>

            @if (! empty($attraction->types))
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($attraction->types as $type)
                        <span class="rounded-full bg-sand px-3 py-1 text-xs font-medium text-navy-800">{{ $type }}</span>
                    @endforeach
                </div>
            @endif
        </x-site.panel>

        @if ($attraction->destinations->isNotEmpty())
            <x-site.panel title="قله‌ها و نقاط نزدیک" icon="mountain">
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($attraction->destinations as $destination)
                        <x-site.link-item
                            :href="route('destinations.show', $destination)"
                            :title="$destination->name"
                            :kicker="$destination->categoryLabel()"
                            :subtitle="$destination->elevation ? fa_num($destination->elevation).' متر' : null"
                            :image="$destination->cover_path"
                        />
                    @endforeach
                </div>
            </x-site.panel>
        @endif

        <x-slot:aside>
            <x-site.map-panel :markers="$mapMarkers" :zoom="11" />

            <x-site.panel title="اطلاعات بازدید" icon="clock">
                <x-site.facts :columns="1" :items="[
                    ['label' => 'نوع جاذبه', 'value' => $attraction->typeLabel()],
                    ['label' => 'دوره', 'value' => $attraction->era],
                    ['label' => 'شهر', 'value' => $attraction->city],
                    ['label' => 'بهترین زمان بازدید', 'value' => $attraction->visiting['best_time'] ?? null],
                    ['label' => 'نکات', 'value' => $attraction->visiting['notes'] ?? null],
                ]" />
                @if ($attraction->mountainRegion)
                    <x-site.link-item class="mt-3" :href="route('regions.show', $attraction->mountainRegion)" :title="$attraction->mountainRegion->name" kicker="منطقه مرتبط" :image="$attraction->mountainRegion->cover_path" />
                @endif
            </x-site.panel>
        </x-slot:aside>
    </x-site.detail-layout>

    <x-site.section spacing="pt-14">
        <livewire:review.section :reviewable="$attraction" :key="'attraction-reviews-'.$attraction->id" />
    </x-site.section>
</x-layouts.site>
