<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\PanelRegistry;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'mobile', 'username', 'email', 'password', 'role', 'primary_club_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function primaryClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'primary_club_id');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->withPivot('status')->withTimestamps();
    }

    public function coachProfile(): HasOne
    {
        return $this->hasOne(CoachProfile::class);
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'owner_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function panelHomeRoute(): string
    {
        return PanelRegistry::homeRouteFor($this);
    }

    public function panelPath(): string
    {
        return PanelRegistry::pathFor($this);
    }

    public function roleLabel(): string
    {
        return $this->role->label();
    }

    public function publicProfileUrl(): string
    {
        return route('profiles.show', ['user' => $this->username]);
    }

    public function initials(): string
    {
        $initials = Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->map(fn (string $part): string => Str::substr($part, 0, 1))
            ->join('');

        return Str::upper(Str::substr($initials, 0, 2));
    }
}
