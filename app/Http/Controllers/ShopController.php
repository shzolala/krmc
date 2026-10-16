<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $shops = Shop::query()
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12);

        return view('shops.index', [
            'shops' => $shops,
        ]);
    }

    public function show(Shop $shop): View
    {
        return view('shops.show', [
            'shop' => $shop,
        ]);
    }
}
