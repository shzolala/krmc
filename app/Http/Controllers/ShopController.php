<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $shops = Shop::query()
            ->withReviewStats()
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->orderByDesc('is_premium')
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shops.index', [
            'shops' => $shops,
            'search' => $search,
        ]);
    }

    public function show(Shop $shop): View
    {
        return view('shops.show', [
            'shop' => $shop,
        ]);
    }
}
