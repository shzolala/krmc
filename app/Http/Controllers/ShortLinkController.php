<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Support\MapLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShortLinkController extends Controller
{
    /**
     * Creates (or reuses) a short link for the map state a visitor wants to share.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['query' => ['present', 'nullable', 'string', 'max:2000']]);

        parse_str((string) $data['query'], $query);

        $link = ShortLink::forMapOptions(MapLink::optionsFromQuery($query), $request->user()?->getKey());

        return response()->json([
            'url' => $link->shortUrl(),
            'qr' => route('map.short.qr', $link->code),
            'download' => route('map.short.qr', [$link->code, 'download' => 1]),
        ]);
    }

    public function show(string $code): RedirectResponse
    {
        $link = ShortLink::query()->where('code', $code)->firstOrFail();
        $link->increment('hits');

        return redirect($link->targetUrl());
    }

    public function qr(Request $request, string $code): Response
    {
        $link = ShortLink::query()->where('code', $code)->firstOrFail();
        $headers = ['Content-Type' => 'image/svg+xml', 'Cache-Control' => 'public, max-age=86400'];

        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="map-'.$link->code.'.svg"';
        }

        return response(MapLink::qrSvg($link->shortUrl(), $request->boolean('download') ? 1024 : 320), 200, $headers);
    }
}
