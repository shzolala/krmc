<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Builds shareable map deep links. The query parameters are read by resources/js/map-explorer.js (readScope).
 */
class MapLink
{
    /** @var list<string> */
    public const BASES = ['topo', 'satellite', 'streets'];

    /**
     * @param  array{types?: list<string>, ids?: list<string>, lat?: float|int|string|null, lng?: float|int|string|null, radius?: float|int|string|null, zoom?: float|int|string|null, focus?: string|null, q?: string|null, base?: string|null}  $options
     */
    public static function url(array $options = []): string
    {
        $hasPoint = is_numeric($options['lat'] ?? null) && is_numeric($options['lng'] ?? null);

        $query = array_filter([
            'types' => self::joined($options['types'] ?? []),
            'ids' => self::joined($options['ids'] ?? []),
            'lat' => $hasPoint ? (float) $options['lat'] : null,
            'lng' => $hasPoint ? (float) $options['lng'] : null,
            'r' => $hasPoint && is_numeric($options['radius'] ?? null) && $options['radius'] > 0 ? (float) $options['radius'] : null,
            'z' => $hasPoint && is_numeric($options['zoom'] ?? null) ? (float) $options['zoom'] : null,
            'focus' => filled($options['focus'] ?? null) ? $options['focus'] : null,
            'q' => filled($options['q'] ?? null) ? trim((string) $options['q']) : null,
            'base' => in_array($options['base'] ?? null, self::BASES, true) ? $options['base'] : null,
        ], fn (mixed $value): bool => $value !== null && $value !== '');

        return route('map', $query);
    }

    /**
     * Normalizes untrusted map query parameters (as used in the URL) into url() options, dropping anything invalid.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public static function optionsFromQuery(array $query): array
    {
        $list = fn (string $key, string $pattern): array => collect(explode(',', (string) ($query[$key] ?? '')))
            ->map(fn (string $item): string => trim($item))
            ->filter(fn (string $item): bool => preg_match($pattern, $item) === 1)
            ->unique()
            ->take(100)
            ->values()
            ->all();
        $number = fn (string $key, float $min, float $max): ?float => is_numeric($query[$key] ?? null) && $query[$key] >= $min && $query[$key] <= $max
            ? (float) $query[$key]
            : null;
        $focus = (string) ($query['focus'] ?? '');

        return [
            'types' => $list('types', '/^[a-z]{2,20}$/'),
            'ids' => $list('ids', '/^[a-z]{2,20}-\d{1,10}$/'),
            'lat' => $number('lat', -90, 90),
            'lng' => $number('lng', -180, 180),
            'radius' => $number('r', 0.1, 500),
            'zoom' => $number('z', 1, 19),
            'focus' => preg_match('/^[a-z]{2,20}-\d{1,10}$/', $focus) === 1 ? $focus : null,
            'q' => mb_substr(trim((string) ($query['q'] ?? '')), 0, 100),
            'base' => in_array($query['base'] ?? null, self::BASES, true) ? $query['base'] : null,
        ];
    }

    public static function qrSvg(string $url, int $size = 320): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 2), new SvgImageBackEnd);

        return (new Writer($renderer))->writeString($url);
    }

    /**
     * @param  list<string>  $values
     */
    private static function joined(array $values): ?string
    {
        $values = array_values(array_filter(array_map('trim', $values)));

        return $values === [] ? null : implode(',', $values);
    }
}
