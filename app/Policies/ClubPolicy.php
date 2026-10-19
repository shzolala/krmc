<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy
{
    public function update(User $user, Club $club): bool
    {
        return $club->owner_id === $user->id;
    }
}
