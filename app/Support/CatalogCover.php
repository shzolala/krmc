<?php

namespace App\Support;

class CatalogCover
{
    /**
     * Local catalog cover images (served from public/images/covers).
     *
     * @var list<string>
     */
    private const COVERS = [
        '/images/covers/cover-01.jpg',
        '/images/covers/cover-02.jpg',
        '/images/covers/cover-03.jpg',
        '/images/covers/cover-04.jpg',
        '/images/covers/cover-05.jpg',
        '/images/covers/cover-06.jpg',
        '/images/covers/cover-07.jpg',
        '/images/covers/cover-08.jpg',
        '/images/covers/cover-09.jpg',
        '/images/covers/cover-10.jpg',
        '/images/covers/cover-11.jpg',
        '/images/covers/cover-12.jpg',
    ];

    public static function for(string $seed): string
    {
        $index = abs(crc32($seed)) % count(self::COVERS);

        return self::COVERS[$index];
    }

    public static function path(string $filename): string
    {
        return '/images/covers/'.$filename;
    }

    /**
     * Photo set for a gallery: the entity cover first, then deterministic catalog photos.
     *
     * @return list<string>
     */
    public static function gallery(?string $cover, string $seed, int $count = 5): array
    {
        $cover ??= self::for($seed);
        $others = array_values(array_filter(self::COVERS, fn (string $path) => $path !== $cover));
        $offset = abs(crc32($seed.'-gallery')) % count($others);
        $rotated = array_merge(array_slice($others, $offset), array_slice($others, 0, $offset));

        return array_slice([$cover, ...$rotated], 0, $count);
    }
}
