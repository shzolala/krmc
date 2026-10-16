@props([
    'title' => null,
    'metaDescription' => 'پلتفرم جامعه کوهنوردان کرمان؛ باشگاه‌ها، مربیان، سرپرستان، مقاصد تخصصی، اقامتگاه‌ها و پناهگاه‌ها.',
    'hideFooter' => false,
])

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription }}">
    <title>{{ $title ? $title.' | '.config('app.name') : config('app.name').' — جامعه کوهنوردان کرمان' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/site.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body @class(['min-h-screen', 'flex h-dvh flex-col overflow-hidden' => $hideFooter])>
    <x-site.header />

    <main @class(['min-h-0 flex-1' => $hideFooter])>
        {{ $slot }}
    </main>

    @unless ($hideFooter)
        <x-site.footer />
    @endunless

    @stack('scripts')
</body>
</html>
