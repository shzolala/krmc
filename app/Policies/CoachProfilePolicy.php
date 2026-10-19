<?php

namespace App\Policies;

use App\Models\CoachProfile;
use App\Models\User;

class CoachProfilePolicy
{
    public function update(User $user, CoachProfile $coachProfile): bool
    {
        return $coachProfile->user_id === $user->id;
    }
}
