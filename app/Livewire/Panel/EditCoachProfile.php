<?php

namespace App\Livewire\Panel;

use App\Models\CoachProfile;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
#[Title('ویرایش پروفایل تخصصی')]
class EditCoachProfile extends Component
{
    public ?int $profileId = null;

    public string $headline = '';

    public string $bio = '';

    public string $specialty = '';

    public int $experience_years = 0;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $profile = CoachProfile::query()->where('user_id', $user->id)->first();

        abort_unless($profile !== null, 404);

        $this->profileId = $profile->id;
        $this->headline = (string) $profile->headline;
        $this->bio = (string) $profile->bio;
        $this->specialty = (string) $profile->specialty;
        $this->experience_years = (int) $profile->experience_years;
    }

    public function save(): void
    {
        $profile = CoachProfile::query()->findOrFail($this->profileId);

        Gate::authorize('update', $profile);

        $validated = $this->validate([
            'headline' => ['required', 'string', 'max:200'],
            'bio' => ['required', 'string', 'max:5000'],
            'specialty' => ['required', 'string', 'max:160'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
        ]);

        $profile->update($validated);

        Flux::toast(variant: 'success', text: 'پروفایل تخصصی ذخیره شد.');
    }

    public function render(): View
    {
        return view('livewire.panel.edit-coach-profile', [
            'profile' => CoachProfile::query()->find($this->profileId),
        ]);
    }
}
