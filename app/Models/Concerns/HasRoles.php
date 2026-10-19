<?php

namespace App\Models\Concerns;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * A person is a single account that may hold several roles at once. Only the administrator is a separate
 * account type; every other role is derived from what the account owns (a club, a shop, a coach profile),
 * so a role can never drift out of sync with the data behind it.
 */
trait HasRoles
{
    /** @var Collection<int, UserRole>|null */
    protected ?Collection $resolvedRoles = null;

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Every role the account holds, specific ones first and the implicit climber baseline last.
     *
     * @return Collection<int, UserRole>
     */
    public function roles(): Collection
    {
        return $this->resolvedRoles ??= $this->resolveRoles();
    }

    /**
     * The roles that describe the person (the implicit climber baseline is left out once they hold a specific one).
     *
     * @return Collection<int, UserRole>
     */
    public function mainRoles(): Collection
    {
        $roles = $this->roles();

        return $roles->count() > 1
            ? $roles->reject(fn (UserRole $role): bool => $role === UserRole::Climber)->values()
            : $roles;
    }

    public function hasRole(UserRole ...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->roles()->contains(fn (UserRole $held): bool => $held === $role)) {
                return true;
            }
        }

        return false;
    }

    public function hasDirectoryProfile(): bool
    {
        return $this->roles()->contains(fn (UserRole $role): bool => $role->hasPublicDirectoryProfile());
    }

    public function roleLabel(): string
    {
        return $this->mainRoles()->map(fn (UserRole $role): string => $role->label())->join('، ');
    }

    public function panelTitle(): string
    {
        return $this->mainRoles()->count() === 1 ? 'پنل '.$this->roleLabel() : 'پنل کاربری';
    }

    /**
     * Drops the memoised roles, e.g. after attaching a club, shop or coach profile to the account.
     */
    public function forgetRoles(): static
    {
        $this->resolvedRoles = null;
        $this->unsetRelation('ownedClubs')->unsetRelation('shops')->unsetRelation('coachProfile');

        return $this;
    }

    #[Scope]
    protected function withRole(Builder $query, UserRole $role): void
    {
        if ($role === UserRole::Admin) {
            $query->where('is_admin', true);

            return;
        }

        $query->where('is_admin', false);

        match ($role) {
            UserRole::Club => $query->whereHas('ownedClubs'),
            UserRole::Shop => $query->whereHas('shops'),
            UserRole::Coach => $query->whereHas('coachProfile', fn (Builder $profile) => $profile->where('is_leader', false)),
            UserRole::Leader => $query->whereHas('coachProfile', fn (Builder $profile) => $profile->where('is_leader', true)),
            default => null,
        };
    }

    /**
     * How many accounts hold each role; accounts holding several roles are counted under each of them.
     *
     * @return Collection<string, int>
     */
    public static function countsByRole(): Collection
    {
        return collect(UserRole::cases())->mapWithKeys(
            fn (UserRole $role): array => [$role->value => static::query()->withRole($role)->count()],
        );
    }

    /**
     * @return Collection<int, UserRole>
     */
    private function resolveRoles(): Collection
    {
        if ($this->isAdmin()) {
            return collect([UserRole::Admin]);
        }

        $roles = collect();

        if ($this->ownedClubs->isNotEmpty()) {
            $roles->push(UserRole::Club);
        }

        if ($this->coachProfile !== null) {
            $roles->push($this->coachProfile->is_leader ? UserRole::Leader : UserRole::Coach);
        }

        if ($this->shops->isNotEmpty()) {
            $roles->push(UserRole::Shop);
        }

        return $roles->push(UserRole::Climber);
    }
}
