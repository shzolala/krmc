<?php

namespace App\Http\Controllers;

use App\Support\MapCatalog;
use Illuminate\View\View;

class MapController extends Controller
{
    public function __invoke(MapCatalog $catalog): View
    {
        $map = $catalog->cached();

        return view('map.index', [
            'mapMarkers' => $map['markers'],
            'counts' => $map['counts'],
            'layers' => $map['layers'],
        ]);
    }
}
