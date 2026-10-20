<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShortLink;
use App\Support\MapCatalog;
use App\Support\MapLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MapLinkController extends Controller
{
    public function __invoke(Request $request, MapCatalog $catalog): View
    {
        $map = $catalog->build();
        $layers = $map['layers'];
        $places = $map['markers']->sortBy('label')->groupBy('type');

        $options = $request->validate([
            'types' => ['nullable', 'array'],
            'types.*' => ['string', Rule::in(array_keys($layers))],
            'ids' => ['nullable', 'array'],
            'ids.*' => ['string', Rule::in($map['markers']->pluck('id')->all())],
            'focus' => ['nullable', 'string', Rule::in($map['markers']->pluck('id')->all())],
            'lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:lng'],
            'lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:lat'],
            'radius' => ['nullable', 'numeric', 'min:0.1', 'max:500'],
            'zoom' => ['nullable', 'numeric', 'between:1,19'],
            'q' => ['nullable', 'string', 'max:100'],
            'base' => ['nullable', Rule::in(MapLink::BASES)],
        ]);

        $generated = $request->query() === [] ? null : MapLink::url($options);
        $short = $generated ? ShortLink::forMapOptions($options, $request->user()->getKey()) : null;

        return view('panel.admin.map-links', [
            'layers' => $layers,
            'places' => $places,
            'options' => $options,
            'url' => $generated,
            'short' => $short,
            'qr' => $short ? MapLink::qrSvg($short->shortUrl()) : null,
        ]);
    }
}
