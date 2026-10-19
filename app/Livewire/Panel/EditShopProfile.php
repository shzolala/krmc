<?php

namespace App\Livewire\Panel;

use App\Models\Shop;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('ویرایش فروشگاه')]
class EditShopProfile extends Component
{
    public ?int $shopId = null;

    public string $name = '';

    public string $summary = '';

    public string $city = '';

    public string $address = '';

    public function mount(Shop $shop): void
    {
        Gate::authorize('update', $shop);

        $this->shopId = $shop->id;
        $this->name = $shop->name;
        $this->summary = (string) $shop->summary;
        $this->city = (string) $shop->city;
        $this->address = (string) $shop->address;
    }

    public function save(): void
    {
        $shop = Shop::query()->findOrFail($this->shopId);

        Gate::authorize('update', $shop);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $shop->update($validated);

        Flux::toast(variant: 'success', text: 'اطلاعات فروشگاه ذخیره شد.');
    }

    public function render(): View
    {
        return view('livewire.panel.edit-shop-profile', [
            'shop' => Shop::query()->find($this->shopId),
        ]);
    }
}
