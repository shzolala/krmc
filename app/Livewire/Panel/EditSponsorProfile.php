<?php

namespace App\Livewire\Panel;

use App\Enums\ProjectCategory;
use App\Enums\SponsorStatus;
use App\Models\Sponsor;
use App\Models\SponsorshipPackage;
use App\Models\SupportProject;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
#[Title('حمایت از کوهستان')]
class EditSponsorProfile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $website = '';

    public string $summary = '';

    public string $description = '';

    public string $supportNote = '';

    public string $preferredCategory = '';

    public ?int $projectId = null;

    public ?int $packageId = null;

    public ?int $pledgeAmount = null;

    public $logo = null;

    public function mount(): void
    {
        $sponsor = $this->sponsor();

        if ($sponsor === null) {
            $this->name = Auth::user()->name;

            return;
        }

        $this->name = $sponsor->name;
        $this->website = (string) $sponsor->website;
        $this->summary = (string) $sponsor->summary;
        $this->description = (string) $sponsor->description;
        $this->supportNote = (string) $sponsor->support_note;
        $this->preferredCategory = (string) $sponsor->preferred_category?->value;
        $this->projectId = $sponsor->support_project_id;
        $this->packageId = $sponsor->sponsorship_package_id;
        $this->pledgeAmount = $sponsor->pledge_amount ?: null;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:160'],
            'website' => ['nullable', 'url', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'supportNote' => ['nullable', 'string', 'max:2000'],
            'preferredCategory' => ['nullable', Rule::enum(ProjectCategory::class)],
            'projectId' => ['nullable', 'integer', 'exists:support_projects,id'],
            'packageId' => ['nullable', 'integer', 'exists:sponsorship_packages,id'],
            'pledgeAmount' => ['required', 'integer', 'min:1'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ], attributes: [
            'name' => 'نام مجموعه',
            'website' => 'وب‌سایت',
            'summary' => 'معرفی کوتاه',
            'pledgeAmount' => 'مبلغ حمایت',
            'logo' => 'لوگو',
        ]);

        $package = $this->packageId ? SponsorshipPackage::query()->active()->find($this->packageId) : null;
        $minimum = $package?->min_amount ?? SponsorshipPackage::minimumPledge();

        if ($this->pledgeAmount < $minimum) {
            throw ValidationException::withMessages([
                'pledgeAmount' => 'مبلغ حمایت نمی‌تواند کمتر از '.fa_money($minimum).' باشد.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $sponsor = $this->sponsor() ?? new Sponsor([
            'user_id' => $user->id,
            'slug' => $this->uniqueSlug($validated['name']),
            'status' => SponsorStatus::Pending,
        ]);

        $sponsor->fill([
            'name' => $validated['name'],
            'website' => $validated['website'] ?: null,
            'summary' => $validated['summary'],
            'description' => $validated['description'] ?: null,
            'support_note' => $validated['supportNote'] ?: null,
            'sponsorship_package_id' => $package?->id,
            'preferred_category' => $validated['preferredCategory'] ?: null,
            'support_project_id' => $validated['projectId'] ?: null,
            'pledge_amount' => $validated['pledgeAmount'],
        ]);

        if ($this->logo !== null) {
            $sponsor->logo_path = $this->logo->store('sponsors', 'public');
        }

        $sponsor->save();
        $this->logo = null;

        Flux::toast(variant: 'success', text: 'اطلاعات حمایت ذخیره شد. پس از تأیید مدیریت و دریافت وجه، در سایت نمایش داده می‌شود.');
    }

    public function render(): View
    {
        $sponsor = $this->sponsor()?->loadSum('incomes', 'amount');

        return view('livewire.panel.edit-sponsor-profile', [
            'sponsor' => $sponsor,
            'packages' => SponsorshipPackage::query()->active()->orderBy('sort_order')->orderBy('min_amount')->get(),
            'minimum' => SponsorshipPackage::minimumPledge(),
            'categories' => ProjectCategory::cases(),
            'projects' => SupportProject::query()->orderBy('title')->get(['id', 'title', 'category']),
        ]);
    }

    private function sponsor(): ?Sponsor
    {
        return Sponsor::query()->where('user_id', Auth::id())->first();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'sponsor';
        $slug = $base;
        $counter = 2;

        while (Sponsor::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
