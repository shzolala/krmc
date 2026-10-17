<?php

namespace App\Livewire\Panel;

use App\Models\Club;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('ویرایش باشگاه')]
class EditClubProfile extends Component
{
    public ?int $clubId = null;

    public string $name = '';

    public string $summary = '';

    public string $description = '';

    public string $city = '';

    public string $address = '';

    public string $province = '';

    public string $manager = '';

    public string $education_head = '';

    public ?int $founded_year = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $club = Club::query()->where('owner_id', $user->id)->first();

        abort_unless($club !== null, 404);

        $this->clubId = $club->id;
        $this->name = $club->name;
        $this->summary = (string) $club->summary;
        $this->description = (string) $club->description;
        $this->city = (string) $club->city;
        $this->address = (string) $club->address;
        $this->province = (string) $club->province;
        $this->manager = (string) $club->manager;
        $this->education_head = (string) $club->education_head;
        $this->founded_year = $club->founded_year;
    }

    public function save(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $club = Club::query()
            ->whereKey($this->clubId)
            ->where('owner_id', $user->id)
            ->firstOrFail();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string', 'max:5000'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:120'],
            'manager' => ['nullable', 'string', 'max:160'],
            'education_head' => ['nullable', 'string', 'max:160'],
            'founded_year' => ['nullable', 'integer', 'min:1300', 'max:1500'],
        ]);

        $club->update($validated);

        Flux::toast(variant: 'success', text: 'اطلاعات باشگاه ذخیره شد.');
    }

    public function render(): View
    {
        return view('livewire.panel.edit-club-profile', [
            'club' => Club::query()->find($this->clubId),
        ]);
    }
}
