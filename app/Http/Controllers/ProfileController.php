<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        $user->loadMissing([
            'primaryClub:id,name,slug',
            'coachProfile.club:id,name,slug',
            'shop:id,name,slug,summary,city',
            'ownedClubs:id,name,slug,summary,city,owner_id',
        ]);

        return view('profiles.show', [
            'profileUser' => $user,
        ]);
    }
}
