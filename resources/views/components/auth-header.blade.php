@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-2 text-start">
    <h1 class="text-3xl font-extrabold text-navy-900">{{ $title }}</h1>
    <p class="text-sm leading-7 text-muted">{{ $description }}</p>
</div>
