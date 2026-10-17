@props([
    'section',
])

<li>
    <a href="{{ route($section['route']) }}" class="group relative isolate flex h-28 flex-col items-center justify-end gap-1.5 overflow-hidden rounded-2xl bg-navy-900 pb-3.5 text-center shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-navy-900/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
        <img src="{{ asset('images/covers/'.$section['image']) }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 group-hover:scale-110">
        <span class="absolute inset-0 -z-10 bg-linear-to-t from-navy-950/90 via-navy-950/45 to-navy-950/10 transition group-hover:from-navy-950/95"></span>
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-accent-soft ring-1 ring-white/25 backdrop-blur transition group-hover:bg-accent group-hover:text-navy-950">
            <x-site.icon :name="$section['icon']" class="h-[18px] w-[18px]" />
        </span>
        <span class="text-sm font-bold text-white">{{ $section['label'] }}</span>
    </a>
</li>
